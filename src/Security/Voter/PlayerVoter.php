<?php

namespace App\Security\Voter;

use App\Entity\Player;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PlayerVoter extends Voter
{
    public const EDIT = 'POST_EDIT';

    protected function supports(string $action, $subject): bool
    {
        $supportedActions = [
            self::EDIT,
        ];
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return (in_array($action, $supportedActions) && $subject instanceof Player);
    }

    protected function voteOnAttribute(string $action, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // pour forcer l'autocomplete sur un objet
        /** @var Player $player */
        $player = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($action) {
            case self::EDIT:
                // Seul l'utilisateur du player doit pouvoir la modifier
                return ($user === $player);
                break;
        }

        return false;
    }
}
