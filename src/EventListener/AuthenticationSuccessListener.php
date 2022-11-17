<?php

namespace App\EventListener;

use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
{
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();

        /** @var \App\Entity\Player */
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $data['data'] = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'nickname' => $user->getNickName(),
            'available' => $user->isAvailable(),
            'discord_tag' => $user->getDiscordTag(),
            'roles' => $user->getRoles()
        ];

        $event->setData($data);
    }
}