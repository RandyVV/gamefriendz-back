<?php

namespace App\Controller;

use App\Entity\Player;
use App\Form\PlayerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register' , methods: ['GET', 'POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $player = new Player();
        $form = $this->createForm(PlayerType::class, $player);

        // Gérer la soumission du formulaire et la validation des données
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            // Hash the password
            $password = $passwordHasher->hashPassword($player, $player->getPassword());
            $player->setPassword($password);
            
            // Save the player to the database
            $entityManager->persist($player);
            $entityManager->flush();

            // Redirect to any route you want after successful registration
            return $this->redirectToRoute('homepage');
        }

        return $this->render('login/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
