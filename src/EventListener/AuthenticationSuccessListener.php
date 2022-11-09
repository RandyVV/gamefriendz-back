<?php

namespace App\EventListener;

use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
{
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $data['data'] = array(
            'id' => $user->getId(),
            'username' => $user->getUserIdentifier(),
            'nickname' => $user->getNickName(),
            'available' => $user->isAvailable(),
            'discord_tag' => $user->getDiscordTag(),
            'owned_games' => $user->getOwnedGames(),
            'wants_to_play' => $user->getWantsToPlay(),
        );

        $event->setData($data);
    }
}