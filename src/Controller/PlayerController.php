<?php

namespace App\Controller;

use App\Entity\Player;
use App\Form\PlayerType;
use App\Security\Voter\PlayerVoter;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\GameOnPlatformRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/player')]
class PlayerController extends AbstractController
{
    #[Route('/', name: 'app_player_index', methods: ['GET'])]
    public function index(PlayerRepository $playerRepository): Response
    {
        return $this->render('player/index.html.twig', [
            'players' => $playerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_player_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PlayerRepository $playerRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $player = new Player();
        $form = $this->createForm(PlayerType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // hacher le mot de passe
            $hashedPassword = $passwordHasher->hashPassword($player, $player->getPassword());
            $player->setPassword($hashedPassword);

            $playerRepository->add($player, true);

            return $this->redirectToRoute('app_player_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('player/new.html.twig', [
            'player' => $player,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_player_show', methods: ['GET'])]
    public function show(Player $player): Response
    {
        return $this->render('player/show.html.twig', [
            'player' => $player,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_player_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Player $player, PlayerRepository $playerRepository): Response
    {
        $form = $this->createForm(PlayerType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $playerRepository->add($player, true);

            return $this->redirectToRoute('app_player_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('player/edit.html.twig', [
            'player' => $player,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_player_delete', methods: ['POST'])]
    public function delete(Request $request, Player $player, PlayerRepository $playerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $player->getId(), $request->request->get('_token'))) {
            $playerRepository->remove($player, true);
        }

        return $this->redirectToRoute('app_player_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/ownedgames', name: 'app_player_ownedgames', methods: ['GET', 'POST'])]
    public function addOwnedGame(Player $player, Request $request, GameOnPlatformRepository $gopRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(PlayerVoter::EDIT, $player, 'Vous ne passerez pas !');

        $postData = $request->toArray();
        $gopId = $postData["id"];
        $gop = $gopRepository->find($gopId);
        $player->addOwnedGame($gop);

        $em->persist($player);
        $em->flush();

        return $this->render('player/ownedgames.html.twig', [
            'player' => $player,
            'game_on_platform' => $gop
        ]);
    }
}
