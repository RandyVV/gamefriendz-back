<?php

namespace App\Controller\Api;

use App\Repository\GameRepository;
use App\Repository\PlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SearchController extends AbstractController
{
    /**
     * @Route("/api/search", name="api_search", methods={"POST"})
     */
    public function search(PlayerRepository $playerRepository, GameRepository $gameRepository, Request $request): JsonResponse
    {
        $jsonContent = $request->getContent();
        $criterias = json_decode($jsonContent, true);

        $term = $criterias['term'] ?? null;
        if ($term === null) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Recherche de joueurs
        $players = $playerRepository->searchPlayers([
            'nickname' => $term,
            // 'discord_tag' => $term
        ]);

        // Recherche de jeux
        $games = $gameRepository->searchGames([
            'title' => $term
        ]);

        $playersGroup = 'players_public';
        if ($this->getUser() !== null) {
            $playersGroup = 'players';
        }

        return $this->json(
            [
                'players' => $players,
                'games' => $games
            ],
            Response::HTTP_OK,
            [],
            ['groups' => [$playersGroup, 'games']]
        );
    }
}
