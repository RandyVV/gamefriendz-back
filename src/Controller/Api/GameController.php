<?php

namespace App\Controller\Api;

use App\Entity\Game;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * Search items
     * 
     * Critères de recherche supportés :
     *  title : recherche dans le titre des jeux
     *  platform : recherche dans le nom des platforms associées aux jeux
     * 
     * @Route("/api/games/search", name="api_games_search", methods={"POST"})
     */
    public function searchItems(GameRepository $gameRepository, Request $request): JsonResponse
    {
        // on récupère les critères de recherche depuis...
        // le contenu POST de la requête
        $jsonContent = $request->getContent();
        // SERIALIZE / ENCODE : ['key' => 'value'] ==> '{"key":"value"}'
        // DESERIALIZE / DECODE : '{"key":"value"}' => ['key' => 'value']
        $criterias = json_decode($jsonContent, true);

        $games = $gameRepository->searchGames($criterias);

        return $this->json(
            $games,
            Response::HTTP_OK,
            [],
            ['groups' => 'games']
        );
    }
}