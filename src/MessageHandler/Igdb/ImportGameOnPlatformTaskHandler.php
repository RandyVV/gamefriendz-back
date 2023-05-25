<?php

namespace App\MessageHandler\Igdb;

use App\Entity\Game;
use App\Entity\Platform;
use App\Entity\GameOnPlatform;
use App\Repository\GameRepository;
use App\Repository\PlatformRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\GameOnPlatformRepository;
use App\Message\Igdb\ImportGameOnPlatformTask;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ImportGameOnPlatformTaskHandler
{
    private $em;
    private $repository;
    private $gameRepository;
    private $platformRepository;

    public function __construct(
        EntityManagerInterface $em,
        GameOnPlatformRepository $repository,
        GameRepository $gameRepository,
        PlatformRepository $platformRepository
    ) {
        $this->em = $em;
        $this->repository = $repository;
        $this->gameRepository = $gameRepository;
        $this->platformRepository = $platformRepository;
    }

    public function __invoke(ImportGameOnplatformTask $task)
    {
        $gopData = $task->getData();

        // 1. récupérer le Game depuis la BDD
        $game = $this->gameRepository->findOneBy(['igdb_id' => $gopData['game_igdb_id']]);

        // 2. récupérer la Platform depuis la BDD
        $platform = $this->platformRepository->findOneBy(['igdb_id' => $gopData['platform_igdb_id']]);

        // 3. on recherche le GameOnPlatform en BDD
        $gop = $this->repository->findOneBy([
            'game' => $game,
            'platform' => $platform
        ]);

        // si le GameOnPlatform n'existe pas en BDD, on le crée
        if ($gop === null) {
            $gop = new GameOnPlatform();
            $gop->setGame($game);
            $gop->setPlatform($platform);
        }

        // on renseigne la date de sortie
        $gop->setReleaseDate($gopData['release_date']);

        $this->em->persist($gop);
        $this->em->flush();
    }

}
