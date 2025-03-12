<?php

namespace App\Application\Security\Strategy\Authentication;

use App\Domain\User\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\User\Repository\UserRepository;
use App\Infrastructure\Security\GoogleAuthService;
use App\Application\Security\DTO\Login\GoogleLoginDTO;
use App\Application\Security\Strategy\AuthenticationStrategyInterface;

class GoogleAuthenticationStrategy implements AuthenticationStrategyInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private GoogleAuthService $googleAuthService
    ) {}

    public function supports(Request $request): bool
    {
        return $request->request->has('id_token');
    }

    public function authenticate(Request $request): ?User
    {
        $dto = GoogleLoginDTO::fromRequest($request);
        $googleUser = $this->googleAuthService->verifyIdToken($dto->idToken);

        if (!$googleUser || $googleUser['email'] !== $dto->email) {
            return null;
        }

        $user = $this->userRepository->findOneBy(['email' => $dto->email]);
        if (!$user) {
            $user = new User();
            $user->setEmail($dto->email);
            $user->setRoles(['ROLE_USER']);
            $user->addAuthProvider('google', ['id_token' => $dto->idToken, 'google_id' => $googleUser['sub']]);
            $this->userRepository->getEntityManager()->persist($user);
            $this->userRepository->getEntityManager()->flush();
        } elseif (!$user->hasAuthProvider('google')) {
            $user->addAuthProvider('google', ['id_token' => $dto->idToken, 'google_id' => $googleUser['sub']]);
            $this->userRepository->getEntityManager()->flush();
        }

        return $user;
    }
}
