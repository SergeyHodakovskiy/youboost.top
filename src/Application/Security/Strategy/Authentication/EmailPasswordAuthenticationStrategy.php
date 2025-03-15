<?php

declare(strict_types=1);

namespace App\Application\Security\Strategy\Authentication;

use App\Application\Security\AuthProvider;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class EmailPasswordAuthenticationStrategy implements AuthenticationStrategyInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly TranslatorInterface $translator
    ) {}

    public function supports(AuthProvider $provider): bool
    {
        return $provider === AuthProvider::EMAIL_PASSWORD;
    }

    public function authenticate(Request $request): ?User
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        if (!$email) {
            throw new AuthenticationException($this->translator->trans('auth.error.missing_email'));
        }

        if (!$password) {
            throw new AuthenticationException($this->translator->trans('auth.error.missing_password'));
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            throw new AuthenticationException($this->translator->trans('auth.error.user_not_found'));
        }

        if (!$user->getPassword() || !$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new AuthenticationException($this->translator->trans('auth.error.invalid_password'));
        }

        return $user;
    }
}
