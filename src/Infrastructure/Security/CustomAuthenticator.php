<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Application\Security\AuthProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Application\Security\Service\AuthenticationService;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class CustomAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly AuthenticationService $authenticationService
    ) {}

    public function supports(Request $request): ?bool
    {
        return preg_match('#^/login/[^/]+$#', $request->getPathInfo()) && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $provider = AuthProvider::fromString($request->attributes->get('provider'));

        $user = $this->authenticationService->authenticate($request, $provider);

        if (!$user) {
            throw new AuthenticationException('Authentication failed');
        }

        return new SelfValidatingPassport(new UserBadge($user->getEmail(), fn() => $user));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($request->headers->get('Accept') === 'application/json') {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_UNAUTHORIZED);
        }

        return new RedirectResponse('/login');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($request->headers->get('Accept') === 'application/json') {
            return new JsonResponse(['message' => 'Logged in successfully']);
        }

        return new RedirectResponse('/dashboard');
    }
}
