<?php

declare(strict_types=1);

namespace App\Application\Security\Strategy\Authentication;

use App\Application\Security\AuthProvider;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepository;
use App\Infrastructure\Security\GoogleAuthService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class GoogleAuthenticationStrategy implements AuthenticationStrategyInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly GoogleAuthService $googleAuthService
    ) {}

    public function supports(AuthProvider $provider): bool
    {
        return $provider === AuthProvider::GOOGLE;
    }

    public function authenticate(Request $request): ?User
    {
        $idToken = $request->request->get('idToken');

        if (!$idToken) {
            throw new AuthenticationException('Google token is missing.');
        }

        $googleUser = $this->googleAuthService->verifyIdToken($idToken);

        if (!$googleUser || !isset($googleUser['email'])) {
            throw new AuthenticationException('Invalid Google token.');
        }

        $email = $googleUser['email'];
        $googleId = $googleUser['sub'];

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            // Создаём нового пользователя, если его нет
            $user = new User();
            $user->setEmail($email);
            $user->setRoles(['ROLE_USER']);
            $user->addAuthProvider('google', ['google_id' => $googleId]);

            $this->userRepository->save($user);
        } elseif (!$user->hasAuthProvider('google')) {
            // Если у пользователя нет Google ID, добавляем его
            $user->addAuthProvider('google', ['google_id' => $googleId]);
            $this->userRepository->save($user);
        }

        return $user;
    }
}
