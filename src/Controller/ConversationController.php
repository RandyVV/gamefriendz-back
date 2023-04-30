<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Message;
use App\Form\MessageType;
use App\Entity\Conversation;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ConversationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConversationController extends AbstractController
{
    #[Route('/conversations', name: 'conversations', methods: ['GET'])]
    public function index(ConversationRepository $conversationRepository): Response
    {
        $user = $this->getUser();
        $conversations = $conversationRepository->findByPlayer($user);

        return $this->render('conversation/index.html.twig', [
            'conversations' => $conversations,
        ]);
    }

    #[Route('/conversation/{id}', name: 'conversation_show', methods: ['GET'])]
    public function show(Conversation $conversation, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MessageType::class, new Message());

        $currentPlayer = $this->getUser();

        // on marque les messages reçus et non-lus comme lus
        $unreadMessages = $conversation->getMessages()
            ->filter(function (Message $message) use ($currentPlayer) {
                return ($message->getAuthor() !== $currentPlayer && $message->getStatus() === Message::STATUS_UNREAD);
            });

        foreach ($unreadMessages as $unreadMessage) {
            $unreadMessage->setStatus(Message::STATUS_READ);
            $em->persist($unreadMessage);
        }

        $em->flush();

        // on récupère l'autre participant de la conversation
        $otherPlayers = $conversation->getParticipate()
            ->filter(function (Player $player) use ($currentPlayer) {
                return $player !== $currentPlayer;
            });


        return $this->render('conversation/show.html.twig', [
            'conversation' => $conversation,
            'form' => $form->createView(),
            'other_players' => $otherPlayers,
        ]);
    }

    #[Route('/conversation/{id}/reply', name: 'conversation_reply', methods: ['POST'])]
    public function reply(Conversation $conversation, Request $request, EntityManagerInterface $em)
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $message->setAuthor($this->getUser());
            $message->setConversation($conversation);
            $message->setCreatedAt(new \DateTimeImmutable());
            $message->setStatus(Message::STATUS_UNREAD);

            $em->persist($message);
            $em->flush();
        }

        return $this->redirectToRoute('conversation_show', ['id' => $conversation->getId()]);
    }
}
