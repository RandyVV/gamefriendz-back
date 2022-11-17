<?php

namespace App\Controller\BackOffice;

use App\Entity\Game;
use App\Form\GameType;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/backoffice/games")
 */
class GameController extends AbstractController
{
    /**
     * @Route("/", name="app_backoffice_game_index", methods={"GET"})
     */
    public function index(GameRepository $gameRepository): Response
    {
        return $this->render('game/index.html.twig', [
            'games' => $gameRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_backoffice_game_new", methods={"GET", "POST"})
     */
    public function new(Request $request, GameRepository $gameRepository, SluggerInterface $slugger): Response
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);

                $formattedFilename = $slugger->slug($originalFilename);
                $newFilename = uniqid($formattedFilename) . '.' . $pictureFile->guessExtension();

                $pictureFile->move(
                    $this->getParameter('game_pictures_directory'),
                    $newFilename
                );

                $pictureUrl = $request->getUriForPath(
                    $this->getParameter('game_pictures_directory_url_path') . $newFilename
                );

                $game->setPicture($pictureUrl);
            }

            $gameRepository->add($game, true);

            return $this->redirectToRoute('app_backoffice_game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/new.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_backoffice_game_show", methods={"GET"})
     */
    public function show(Game $game): Response
    {
        return $this->render('game/show.html.twig', [
            'game' => $game,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_backoffice_game_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Game $game, GameRepository $gameRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);

                $formattedFilename = $slugger->slug($originalFilename);
                $newFilename = uniqid($formattedFilename) . '.' . $pictureFile->guessExtension();

                $pictureFile->move(
                    $this->getParameter('game_pictures_directory'),
                    $newFilename
                );

                $pictureUrl = $request->getUriForPath(
                    $this->getParameter('game_pictures_directory_url_path') . $newFilename
                );

                $game->setPicture($pictureUrl);
            }

            $gameRepository->add($game, true);

            return $this->redirectToRoute('app_backoffice_game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/edit.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_backoffice_game_delete", methods={"POST"})
     */
    public function delete(Request $request, Game $game, GameRepository $gameRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$game->getId(), $request->request->get('_token'))) {
            $gameRepository->remove($game, true);
        }

        return $this->redirectToRoute('app_backoffice_game_index', [], Response::HTTP_SEE_OTHER);
    }
}
