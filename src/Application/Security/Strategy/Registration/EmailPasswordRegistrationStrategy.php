<?php

namespace App\Security\Strategy\Registration;

use App\Core\Entity\User;
use App\Core\Repository\UserRepository;
use App\Core\Service\PreferenceService;
use App\Security\DTO\EmailPassword\RegisterRequestDTO;
use Symfony\Component\HttpFoundation\Request;
use App\Security\Validator\RegistrationValidator;
use App\Security\Strategy\RegistrationStrategyInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EmailPasswordRegistrationStrategy implements RegistrationStrategyInterface
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private RegistrationValidator $validator;
    private PreferenceService $preferenceService;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        RegistrationValidator $validator,
        PreferenceService $preferenceService
    ) {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->validator = $validator;
        $this->preferenceService = $preferenceService;
    }

    public function supports($data): bool
    {
        return  $data instanceof RegisterRequestDTO ||
            ($data instanceof Request &&
                $data->isMethod('POST') &&
                $data->request->has('email') &&
                $data->request->has('password') &&
                $data->request->has('password_confirm')
            );
    }

    public function register($data): ?User
    {
        $dto = $data instanceof RegisterRequestDTO ? $data : $this->createDTOFromRequest($data);
        $request = $data instanceof Request ? $data : null;

        $errors = $this->validator->validateDTO($dto); // Обновим валидатор
        if (!empty($errors)) {
            $request?->getSession()->set('validation_errors', $errors);
            return null;
        }

        if ($this->userRepository->findOneBy(['email' => $dto->email])) {
            $request?->getSession()->set('validation_errors', ['email' => 'Этот email уже занят']);
            return null;
        }

        $user = new User();
        $user->setEmail($dto->email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $dto->password));
        $user->setRoles(['ROLE_USER']);

        $this->preferenceService->updatePreferences($user, [
            'name' => $dto->name,
            'theme' => $dto->theme,
        ]);

        $this->userRepository->save($user);
        return $user;
    }

    private function createDTOFromRequest(Request $request): RegisterRequestDTO
    {
        return new RegisterRequestDTO(
            $request->request->get('email', ''),
            $request->request->get('password', ''),
            $request->request->get('password_confirm', ''),
            $request->request->get('name', ''),
            $request->request->get('theme', $request->cookies->get('theme', 'light'))
        );
    }
}
