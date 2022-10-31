<?php

namespace App\Controller\Api;

use App\Repository\PlatformRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PlatformController extends AbstractController
{
    /**
     * @Route("/api/platforms", name="app_api_platforms_get_collection")
     */
    public function getCollection(PlatformRepository $platformRepository): JsonResponse
    {
        $platforms = $platformRepository->findAll();

        return $this->json(
            $platforms,
            Response::HTTP_OK,
            [],
            ['groups' => 'platforms']
        );
    }

}

