<?php

namespace App\Command;

use DateTime;
use App\Entity\Game;
use App\Entity\Message;
use App\Entity\Platform;
use App\Service\IgdbService;
use App\Entity\GameOnPlatform;
use Doctrine\ORM\EntityManager;
use App\Message\Igdb\ImportGameTask;
use App\Message\RunImportGamesCommand;
use App\Repository\PlatformRepository;
use App\Message\Igdb\ImportPlatformTask;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportGamesCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:import-games';

    /**
     * Service permettant de dialoguer avec l'API IGDB
     * @var IgdbService
     */
    private $igdbService;

    /**
     * Entity manager pour sauvegarder les entités
     * @var EntityManager
     */
    private $em;

    /**
     * Message bus
     */
    private $bus;
    
    public function __construct(IgdbService $igdbService, EntityManagerInterface $em, MessageBusInterface $bus)
    {
        $this->igdbService = $igdbService;
        $this->em = $em;
        $this->bus = $bus;

        parent::__construct();        
    }

    /**
     * Configure les options de la commande
     */
    protected function configure()
    {
        $this->setHelp("Lance l'import de jeux vidéo depuis l'API IGDB");

        $this->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, "Nombre maximum de jeux à importer par requête à l'API", 500);
        $this->addOption('offset', 'o', InputOption::VALUE_OPTIONAL, "Position à partir de laquelle récupérer les jeux", 0);

        $this->addOption('max', 'm', InputOption::VALUE_OPTIONAL, "Nombre maximum de jeux à importer en tout, passer -1 pour tout importer", 1000);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // on va chercher les jeux sur l'api IGDB
        $limit = $input->getOption('limit');
        $offset = $input->getOption('offset');
        $quota = $input->getOption('max');

        $gamesData = $this->igdbService->getGames($limit, $offset);
        $remaining = ($quota < 0 ? INF : $quota);

        // créer les entités Game à partir des données récupérées
        foreach ($gamesData as $gameData)
        {
            // pour chaque jeu traité, on retire 1 au quota
            $remaining--;
            if ($remaining < 1) {
                break;
            }

            // on n'importe pas les jeux qui :
            // - n'ont pas d'image
            // - n'ont pas de date de sortie (impossible de créer les GameOnPlatform)
            if (!isset($gameData->cover) || !isset($gameData->release_dates)) {
                continue;
            }

            // dates de sortie du jeu par plate-forme
            $gameReleases = [];

            foreach ($gameData->release_dates as $releaseData) {

                if (!isset($releaseData->date)) {
                    continue;
                }

                $this->bus->dispatch(new ImportPlatformTask([
                    'name' => $releaseData->platform->name,
                    'igdb_id' => $releaseData->platform->id,
                    'slug' => $releaseData->platform->slug
                ]));

                // on transforme le timestamp en date
                $releaseDate = DateTime::createFromFormat('U', $releaseData->date);

                // on ajoute la date de sortie aux dates de sortie du jeu
                $gameReleases[$releaseData->platform->id] = $releaseDate;
            }

            // un jeu supporte le multijoueur si :
            // - l'information "multiplayer_modes" existe dans les données de l'API
            // - l'information "mutliplayer_modes" n'est pas vide
            $hasMultiplayerMode = isset($gameData->multiplayer_modes) && !empty($gameData->multiplayer_modes);

            // on prépare l'URL de l'image du jeu
            $pictureUrl = 'https://images.igdb.com/igdb/image/upload/t_1080p/' . $gameData->cover->image_id . '.jpg';

            // par défaut, le titre du jeu est son titre générique de l'API IGDB
            $gameTitle = $gameData->name;

            // si le jeu a des noms alternatifs, on vérifie s'il a un nom français
            if (isset($gameData->alternative_names)) {
                foreach ($gameData->alternative_names as $alternativeName) {

                    if (isset($alternativeName->comment) && $alternativeName->comment === 'French title') {

                        $gameTitle = $alternativeName->name;
                        break;
                    }
                }
            }

            $this->bus->dispatch(new ImportGameTask([
                'title' => $gameTitle,
                'description' => $gameData->summary ?? '',
                'has_multiplayer_mode' => $hasMultiplayerMode,
                'picture' => $pictureUrl,
                'igdb_id' => $gameData->id,
                'releases' => $gameReleases
            ]));
        }

        // on relance la commande de manière asynchrone seulement si :
        // - on n'a pas atteint le nombre maximum de jeux à importer
        // - on n'a pas atteint le dernier "paquet" de jeux de l'API
        if ($remaining > 0 && count($gamesData) == $limit) {

            $this->bus->dispatch(new RunImportGamesCommand([
                // si le nombre de jeux maximum restant à importer est inférieur à la limite par import, on limite au quota
                'limit' => ($remaining < $limit ? $remaining : $limit),
                // on "décale" les résultats pour récupérer les jeux suivants
                'offset' => $offset + $limit,
                // on indique combien de jeux il reste à importer au maximum (si c'est limité)
                'max' => ($quota < 0 ? $quota : $remaining)
            ]));
        }

        return Command::SUCCESS;
    }
}