<?php

namespace App\Controller\Api;

use App\Entity\Player;
use App\Repository\PlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PlayerController extends AbstractController
{
    /**
     * Get Item
     * 
     * @Route("/api/players/{id}", name="app_api_players_get_item", methods={"GET"})
     */
    public function getItem(Player $player = null): JsonResponse
    {
        // 404 ?
        if (null === $player) {
            throw $this->createNotFoundException('Joueur non trouvé');
        }

        return $this->json(
            $player,
            Response::HTTP_OK,
            [],
            ['groups' => 'player']
        );
    }
}