<?php

namespace App\Controller\Api;

use App\Entity\Player;
use App\Form\PlayerType;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\GameOnPlatformRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class PlayerController extends AbstractController
{
    /**
     * @Route("/api/players", name="api_players")
     */
    public function getCollection(PlayerRepository $playerRepository): JsonResponse
    {
        $players = $playerRepository->findAll();

        return $this->json(
            $players,
            Response::HTTP_OK,
            [],
            ['groups' => 'player']
        );
    }


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

    /**
     * Search items
     * 
     * Critères de recherche supportés :
     *  nickname : recherche par pseudo
     *  discord_tag : recherche par le tag discord d'un joueur
     * 
     * @Route("/api/players/search", name="api_players_search", methods={"POST"})
     */
    public function searchItems(PlayerRepository $playerRepository, Request $request): JsonResponse
    {
        // on récupère les critères de recherche depuis...
        // le contenu POST de la requête
        $jsonContent = $request->getContent();
        // SERIALIZE / ENCODE : ['key' => 'value'] ==> '{"key":"value"}'
        // DESERIALIZE / DECODE : '{"key":"value"}' => ['key' => 'value']
        $criterias = json_decode($jsonContent, true);

        $players = $playerRepository->searchPlayers($criterias);

        return $this->json(
            $players,
            Response::HTTP_OK,
            [],
            ['groups' => 'players']
        );
    }

    /**
     * @Route("/api/new/player", name="api_new_player", methods={"GET", "POST"})
     */
    public function new(Request $request, PlayerRepository $playerRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $player = new Player();
        $form = $this->createForm(PlayerType::class, $player);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // on hâche le mot de passe
            $hashedPassword = $passwordHasher->hashPassword($player, $player->getPassword());
            // on écrase le mot de passe dans le User
            $player->setPassword($hashedPassword);

            $playerRepository->add($player, true);
        }
        return $this->json(
            $player,
            Response::HTTP_CREATED,
            [],
            ['groups' => 'authenticate']
        );
    }
}
