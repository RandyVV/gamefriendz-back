<?php

namespace App\MessageHandler\Igdb;

use App\Entity\Game;
use App\Entity\Platform;
use App\Repository\GameRepository;
use App\Message\Igdb\ImportGameTask;
use Doctrine\ORM\EntityManagerInterface;
use App\Message\Igdb\ImportGameOnPlatformTask;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ImportGameTaskHandler
{
    private $em;
    private $repository;
    private $bus;

    public function __construct(EntityManagerInterface $em, GameRepository $repository, MessageBusInterface $bus )
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->bus = $bus;
    }

    public function __invoke(ImportGameTask $task)
    {
        $gameData = $task->getData();

        // 1. rechercher le Game en BDD
        $game = $this->repository->findOneBy(['igdb_id' => $gameData['igdb_id']]);

        // 2. si le Game n'existe pas en BDD, on le crée
        if ($game === null) {
            $game = new Game();
            $game->setTitle($gameData['title']);
            $game->setDescription($gameData['description']);
            $game->setHasMultiplayerMode($gameData['has_multiplayer_mode']);
            $game->setPicture($gameData['picture']);
            $game->setIgdbId($gameData['igdb_id']);

            $this->em->persist($game);
            $this->em->flush();
        }

        // on extrait les dates de sorties du jeu
        $gameReleases = $gameData['releases'];

        foreach ($gameReleases as $platformIgdbId => $releaseDate) {

            $this->bus->dispatch(new ImportGameOnPlatformTask([
                'release_date' => $releaseDate,
                'game_igdb_id' => $gameData['igdb_id'],
                'platform_igdb_id' => $platformIgdbId
            ]));
        }
    }
}