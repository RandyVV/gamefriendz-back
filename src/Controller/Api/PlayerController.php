<?php

namespace App\Controller\Api;

use App\Entity\Player;
use App\Repository\GameOnPlatformRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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


    /**
     * @Route("/api/players/{id}/addownedgames", methods={"POST"}, name="api_players_single_add_ownedgame")
     */
    public function addOwnedGame(Player $player, Request $request, GameOnPlatformRepository $gopRepository, EntityManagerInterface $em)
    {
        // On met dans une variable le contenu de la requete post sous forme de tableau
        $postData = $request->toArray();
        
        $gopId = $postData["id"];
        
        $gop = $gopRepository->find($gopId);

        $player->addOwnedGame($gop);

        $em->persist($player);
        $em->flush();

        return $this->json(
            $player,
            Response::HTTP_CREATED,
            [],
            ['groups' => 'player']
        );
    }

    /**
     * @Route("/api/players/{id}/addwantstoplay", methods={"POST"}, name="api_players_single_add_wantstoplay")
     */
    public function addWantsToPlay(Player $player, Request $request, GameOnPlatformRepository $gopRepository, EntityManagerInterface $em)
    {
        // On met dans une variable le contenu de la requete post sous forme de tableau
        $postData = $request->toArray();

        $gopId = $postData["id"];

        $gop = $gopRepository->find($gopId);

        $player->addWantsToPlay($gop);

        $em->persist($player);
        $em->flush();

        return $this->json(
            $player,
            Response::HTTP_CREATED,
            [],
            ['groups' => 'player']
        );
    }

    /**
     * @Route("/api/players/{id}/removeownedgames", methods={"DELETE"}, name="api_players_single_remove_ownedgame")
     */
    public function removeOwnedGame(Player $player, Request $request, GameOnPlatformRepository $gopRepository, EntityManagerInterface $em)
    {
        // On met dans une variable le contenu de la requete post sous forme de tableau
        $postData = $request->toArray();

        $gopId = $postData["id"];

        $gop = $gopRepository->find($gopId);

        $player->removeOwnedGame($gop);

        $em->persist($player);
        $em->flush();

        return $this->json(
            $player,
            Response::HTTP_ACCEPTED,
            [],
            ['groups' => 'player']
        );
    }

    /**
     * @Route("/api/players/{id}/removewantstotlay", methods={"DELETE"}, name="api_players_single_remove_wantstoplay")
     */
    public function removeWantsToPlay(Player $player, Request $request, GameOnPlatformRepository $gopRepository, EntityManagerInterface $em)
    {
        // On met dans une variable le contenu de la requete post sous forme de tableau
        $postData = $request->toArray();

        $gopId = $postData["id"];

        $gop = $gopRepository->find($gopId);

        $player->removeWantsToPlay($gop);

        $em->persist($player);
        $em->flush();

        return $this->json(
            $player,
            Response::HTTP_ACCEPTED,
            [],
            ['groups' => 'player']
        );
    }

    public function searchPlayer(Player $player)
    {

    }
}

