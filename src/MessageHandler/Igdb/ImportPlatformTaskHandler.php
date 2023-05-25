<?php

namespace App\MessageHandler\Igdb;

use App\Entity\Platform;
use App\Repository\PlatformRepository;
use App\Message\Igdb\ImportPlatformTask;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ImportPlatformTaskHandler
{
    private $em;
    private $repository;

    public function __construct(EntityManagerInterface $em, PlatformRepository $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    public function __invoke(ImportPlatformTask $task)
    {
        $platformData = $task->getData();

        // 1. rechercher en BDD la plate-forme
        $platform = $this->repository->findOneBy(['igdb_id' => $platformData['igdb_id']]);

        // 2. si la plate-forme n'existe pas en BDD, on la crée
        if ($platform === null) {
            $platform = new Platform();
            $platform->setName($platformData['name']);
            $platform->setIgdbId($platformData['igdb_id']);
            $platform->setSlug($platformData['slug']);
            
            $this->em->persist($platform);
            $this->em->flush();
        }
    }
}