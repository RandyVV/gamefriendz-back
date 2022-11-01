<?php

namespace App\Controller\Api;

use App\Entity\Game;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GameController extends AbstractController
{
    /**
     * @Route("/api/games", name="api_games")
     */
    public function getCollection(GameRepository $gameRepository): JsonResponse
    {
        $games = $gameRepository->findAll();

        return $this->json(
            $games,
            Response::HTTP_OK,
            [],
            ['groups' => 'games']
        );
    }

    /**
     * Get Item
     *
     * @Route("/api/games/{id}", name="api_games_single", methods={"GET"})
     */
    public function getItem(Game $game = null): JsonResponse
    {
        // 404 ?
        if (null === $game) {
            throw $this->createNotFoundException('Jeu non trouvé');
        }

        return $this->json(
            $game,
            Response::HTTP_OK,
            [],
            ['groups' => 'game']
        );
    }
}