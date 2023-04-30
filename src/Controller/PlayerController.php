<?php

namespace App\Controller;

use App\Entity\Player;
use App\Form\PlayerType;
use Psr\Log\LoggerInterface;
use App\Service\ImageResizer;
use App\Form\PlayerAvatarType;
use App\Form\SearchPlayerType;
use App\Security\Voter\PlayerVoter;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ConversationRepository;
use App\Repository\GameOnPlatformRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
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

    #[Route('/{id}', name: 'app_player_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Player $player, ConversationRepository $conversationRepository): Response
    {
        $conversation = $conversationRepository->findByPlayers($this->getUser(), $player);

        return $this->render('player/show.html.twig', [
            'player' => $player,
            'conversation' => $conversation,
        ]);
    }


    #[Route('/{id}/ownedgames', name: 'app_player_ownedgames', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function addOwnedGame(Player $player, Request $request, GameOnPlatformRepository $gopRepository, EntityManagerInterface $em): Response
    {
        $currentPlayer = $this->getUser();

        if (!$player || !$currentPlayer || $player !== $currentPlayer) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour ajouter un jeu à la liste ownedGames.');
        }

        $gop = $gopRepository->find($request->request->get('id'));
        $player->addOwnedGame($gop);

        $em->persist($player);
        $em->flush();

        $this->addFlash('success', 'Le jeu a été ajouté à ma liste avec succès.');

        return $this->redirectToRoute('app_game_show', ['id' => $gop->getGame()->getId()]);
    }


    #[Route('/{id}/wantstoplay', name: 'app_player_wantstoplay', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function addWantsToPlay(Player $player, Request $request, GameOnPlatformRepository $gopRepository, EntityManagerInterface $em): Response
    {
        $currentPlayer = $this->getUser();

        if (!$player || !$currentPlayer || $player !== $currentPlayer) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour ajouter un jeu à la liste ownedGames.');
        }

        $gop = $gopRepository->find($request->request->get('id'));
        $player->addWantsToPlay($gop);

        $em->persist($player);
        $em->flush();

        $this->addFlash('success', 'Le jeu a été ajouté à ma liste des envies avec succès.');

        return $this->redirectToRoute('app_game_show', ['id' => $gop->getGame()->getId()]);
    }

    #[Route('/{id}/ownedgames/remove/{redirectRoute}/{redirectId}', name: 'app_player_ownedgames_remove', methods: ['POST'], requirements: ['id' => '\d+', 'redirectRoute' => 'app_game_show|app_player_profile', 'redirectId' => '\d+'])]
    public function removeOwnedGame(Player $player, Request $request, GameOnPlatformRepository $gopRepository, EntityManagerInterface $em, string $redirectRoute, int $redirectId): Response
    {
        $currentPlayer = $this->getUser();

        if (!$player || !$currentPlayer || $player !== $currentPlayer) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour supprimer un jeu de la liste.');
        }

        $gop = $gopRepository->find($request->request->get('id'));
        $player->removeOwnedGame($gop);

        $em->persist($player);
        $em->flush();

        $this->addFlash('success', 'Le jeu a été supprimé de ma liste avec succès.');

        return $this->redirectToRoute($redirectRoute, ['id' => $redirectId]);
    }

    #[Route('/{id}/wantstoplay/remove/{redirectRoute}/{redirectId}', name: 'app_player_wantstoplay_remove', methods: ['POST'], requirements: ['id' => '\d+', 'redirectRoute' => 'app_game_show|app_player_profile', 'redirectId' => '\d+'])]
    public function removeWantsToPlay(Player $player, Request $request, GameOnPlatformRepository $gopRepository, EntityManagerInterface $em, string $redirectRoute, int $redirectId): Response
    {
        $currentPlayer = $this->getUser();

        if (!$player || !$currentPlayer || $player !== $currentPlayer) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour supprimer un jeu de la liste auxquels vous voulez jouer.');
        }

        $gop = $gopRepository->find($request->request->get('id'));
        $player->removeWantsToPlay($gop);

        $em->persist($player);
        $em->flush();

        $this->addFlash('success', 'Le jeu a été supprimé de ma liste des envies avec succès.');

        return $this->redirectToRoute($redirectRoute, ['id' => $redirectId]);
    }

    #[Route('/search', name: 'app_player_search', methods: ['GET', 'POST'])]
    public function search(Request $request, PlayerRepository $playerRepository): Response
    {
        // Vérifiez si l'utilisateur est connecté
        if (!$this->getUser()) {
            throw new AccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        $form = $this->createForm(SearchPlayerType::class);
        $form->handleRequest($request);

        $players = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $criterias = $form->getData();
            $players = $playerRepository->searchPlayers($criterias);
        }

        // Récupérez la liste de joueurs pour l'auto-complétion
        $playerList = $playerRepository->findAll();

        return $this->render('player/search_form.html.twig', [
            'searchForm' => $form->createView(),
            'players' => $players,
            'playerList' => $playerList, // Passez la liste des joueurs à votre template Twig
        ]);
    }

    #[Route('/{id}/profile', name: 'app_player_profile', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function profile(int $id, Player $player, Security $security, PlayerRepository $playerRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur courant est autorisé à afficher le profil
        $currentUser = $security->getUser();
        if ($currentUser !== $player && !$security->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException("Vous n'êtes pas autorisé à accéder à ce profil.");
        }

        if (!$player) {
            throw $this->createNotFoundException('Le joueur demandé n\'existe pas.');
        }

        // Créez le formulaire
        $form = $this->createForm(PlayerAvatarType::class, $player);

        // Traitez la soumission du formulaire (si applicable)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Mettez à jour l'avatar et enregistrez les modifications dans la base de données
            $entityManager->flush();

            return $this->redirectToRoute('app_player_profile', ['id' => $player->getId()]);
        }

        // Récupérez la liste des jeux possédés et souhaités en utilisant les nouvelles méthodes
        $ownedGames = $playerRepository->findOwnedGames($id);
        $wantsToPlay = $playerRepository->findWantedGames($id);

        return $this->render('player/profile.html.twig', [
            'player' => $player,
            'form' => $form->createView(),
            'ownedGames' => $ownedGames,
            'wantsToPlay' => $wantsToPlay,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_player_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Player $player, PlayerRepository $playerRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $currentUser = $this->getUser();
        if (!$player || !$currentUser || $player !== $currentUser) {
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que votre propre profil.');
        }

        $form = $this->createForm(PlayerType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le champ de mot de passe du formulaire
            $password = $form->get('password')->getData();
            // Hacher le mot de passe seulement s'il est renseigné
            if ($password) {
                $hashedPassword = $passwordHasher->hashPassword($player, $password);
                $player->setPassword($hashedPassword);
            }

            $playerRepository->add($player, true);

            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');

            return $this->redirectToRoute('app_player_profile', ['id' => $player->getId()]);
        }

        return $this->renderForm('player/edit.html.twig', [
            'player' => $player,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_player_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Player $player, PlayerRepository $playerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $player->getId(), $request->request->get('_token'))) {
            $playerRepository->remove($player, true);
        }

        return $this->redirectToRoute('app_player_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/available', name: 'app_player_available', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function available(Player $player, EntityManagerInterface $em): Response
    {
        $player->setAvailable(!$player->isAvailable());
        $em->persist($player);
        $em->flush();

        return $this->redirectToRoute('app_player_profile', ['id' => $player->getId()]);
    }

    /**
     * @Route("/{id}/update-avatar", name="player_update_avatar", methods={"POST"})
     */
    public function updatePlayerAvatar(Player $player, Request $request, EntityManagerInterface $em, SluggerInterface $slugger, ImageResizer $imageResizer)
    {
        $this->denyAccessUnlessGranted(PlayerVoter::EDIT, $player, 'Vous ne passerez pas !');

        $form = $this->createForm(PlayerAvatarType::class, $player);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Symfony\Component\HttpFoundation\UploadedFile $pictureFile */
            $pictureFile = $form->get('avatar')->getData();

            $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);

            $formattedFilename = $slugger->slug($originalFilename);
            $newFilename = uniqid($formattedFilename) . '.' . $pictureFile->guessExtension();

            $pictureFile->move(
                $this->getParameter('avatar_pictures_directory'),
                $newFilename
            );

            // Create the resized directory if it doesn't exist
            $resizedDirectory = $this->getParameter('avatar_pictures_directory') . '/resized';
            if (!file_exists($resizedDirectory)) {
                mkdir($resizedDirectory, 0755, true);
            }

            // Resize the uploaded image
            $uploadedImagePath = $this->getParameter('avatar_pictures_directory') . '/' . $newFilename;
            $resizedImagePath = $this->getParameter('avatar_pictures_directory') . '/resized/' . $newFilename;
            $imageResizer->resize($uploadedImagePath, $resizedImagePath, 200, 200);

            $pictureUrl = $request->getUriForPath(
                $this->getParameter('avatar_pictures_directory_url_path') . 'resized/' . $newFilename
            );

            $player->setAvatar($pictureUrl);
            $em->persist($player);
            $em->flush();

            $this->addFlash('success', 'Avatar mis à jour avec succès.');

            return $this->redirectToRoute('app_player_profile', ['id' => $player->getId()]);
        }
    }
}
