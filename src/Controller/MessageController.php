<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Message;
use App\Form\MessageType;
use App\Entity\Conversation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{
    /**
     * @Route("/player/{id}/message", name="send_message", methods={"GET", "POST"})
     */
    public function sendMessage(Request $request, Player $player, EntityManagerInterface $entityManager): Response
    {

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPlayer = $this->getUser();

            $conversation = new Conversation();
            $conversation->addParticipate($currentPlayer);
            $conversation->addParticipate($player);

            $message->setAuthor($currentPlayer);
            $message->setConversation($conversation);
            $message->setCreatedAt(new \DateTimeImmutable());
            $message->setStatus(Message::STATUS_UNREAD);

            $entityManager->persist($conversation);
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('conversation_show', ['id' => $conversation->getId()]);
        }

        return $this->render('message/send.html.twig', [
            'form' => $form->createView(),
            'player' => $player,
            'action' => $this->generateUrl('send_message', ['id' => $player->getId()])
        ]);
    }
}
