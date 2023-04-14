<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameType;
use App\Form\SearchGameType;
use App\Repository\GameRepository;
use App\Repository\PlatformRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/game')]
class GameController extends AbstractController
{
    // #[Route('/', name: 'app_game_index', methods: ['GET'])]
    // public function index(GameRepository $gameRepository): Response
    // {
    //     return $this->render('game/index.html.twig', [
    //         'games' => $gameRepository->findAll(),
    //     ]);
    // }

    // #[Route('/new', name: 'app_game_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, GameRepository $gameRepository): Response
    // {
    //     $game = new Game();
    //     $form = $this->createForm(GameType::class, $game);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $gameRepository->add($game, true);

    //         return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('game/new.html.twig', [
    //         'game' => $game,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}', name: 'app_game_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Game $game): Response
    {
        $currentPlayer = $this->getUser();
 
        return $this->render('game/show.html.twig', [
            'game' => $game,
            'current_player' => $currentPlayer,
        ]);
    }


    // #[Route('/{id}/edit', name: 'app_game_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    // public function edit(Request $request, Game $game, GameRepository $gameRepository): Response
    // {
    //     $form = $this->createForm(GameType::class, $game);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $gameRepository->add($game, true);

    //         return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('game/edit.html.twig', [
    //         'game' => $game,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_game_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    // public function delete(Request $request, Game $game, GameRepository $gameRepository): Response
    // {
    //     if ($this->isCsrfTokenValid('delete' . $game->getId(), $request->request->get('_token'))) {
    //         $gameRepository->remove($game, true);
    //     }

    //     return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
    // }

    #[Route('/search', name: 'app_game_search', methods: ['GET', 'POST'])]
    public function search(Request $request, GameRepository $gameRepository, PlatformRepository $platformRepository): Response
    {
        $platforms = $platformRepository->findAll();

        $form = $this->createForm(SearchGameType::class, null, [
            'platforms' => $platforms,
        ]);
        $form->handleRequest($request);

        $games = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $criterias = $form->getData();
            $games = $gameRepository->searchGames($criterias);
        }

        return $this->render('game/search_form.html.twig', [
            'searchForm' => $form->createView(),
            'games' => $games,
        ]);
    }
}
