<?php

namespace App\Security;

use App\Entity\Player;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class AppPlayerAuthenticator extends AbstractAuthenticator
{
    public function supports(Request $request): ?bool
    {
        // si la requête comporte /login et est en post, on veut essayer d'authentifier
        return $request->getPathInfo() === '/login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {   
        $apiToken = $request->headers->get('X-AUTH-TOKEN');

        if (null === $apiToken) {
            // Le token header était vide, authentication éhoue et 401
            // Code 401 "Unauthorized"
            throw new CustomUserMessageAuthenticationException('No API token provided // pas de token, pas de chocolat');
        }

        $email = $request->request->get('email');
        $password = $request->request->get('password');

        return new Passport(
            new UserBadge($apiToken), 
            new CustomCredentials(function($password, Player $player) {
                dd($password, $player);
            }, $password)
        );
    } 

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // si validé / authentifié

        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // i non authentifié

        $interdiction = ['message' => strtr($exception->getMessageKey(), $exception->getMessageData())];

        return new JsonResponse($interdiction, Response::HTTP_UNAUTHORIZED);
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }

}