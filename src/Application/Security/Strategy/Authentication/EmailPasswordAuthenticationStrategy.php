<?php

namespace App\Application\Security\Strategy\Authentication;

use App\Domain\User\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\User\Repository\UserRepository;
use App\Application\Security\DTO\Login\EmailPasswordLoginDTO;
use App\Application\Security\Strategy\AuthenticationStrategyInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EmailPasswordAuthenticationStrategy implements AuthenticationStrategyInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function supports(Request $request): bool
    {
        return $request->request->has('_username') && $request->request->has('_password');
    }

    public function authenticate(Request $request): ?User
    {
        $dto = EmailPasswordLoginDTO::fromRequest($request);

        $user = $this->userRepository->findOneBy(['email' => $dto->email]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, plainPassword: $dto->password)) {
            return null;
        }

        return $user;
    }
}
