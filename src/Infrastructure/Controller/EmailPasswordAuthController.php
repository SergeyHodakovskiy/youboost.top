<?php

namespace App\Infrastructure\Controller;

use App\Application\Security\Provider;
use App\Application\Security\Service\AuthenticationService;
use App\Application\Security\Service\RegistrationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EmailPasswordAuthController extends AbstractController
{
    #[Route('/login/email-password', name: 'app_login_email_password', methods: ['POST'])]
    public function login(Request $request, AuthenticationService $authenticationService): Response
    {
        $authenticationService->setProvider(Provider::EMAIL_PASSWORD);

        $user = $authenticationService->authenticate($request);

        if (!$user) {
            return $this->json(['error' => 'Authentication failed'], 401);
        }

        return $this->json(['message' => 'Logged in successfully', 'user' => $user->getEmail()]);
    }

    #[Route('/register/email-password', name: 'app_register_email_password', methods: ['POST'])]
    public function register(Request $request, RegistrationService $registrationService): Response
    {
        $registrationService->setProvider(Provider::EMAIL_PASSWORD);

        $user = $registrationService->register($request);

        return $this->json(['message' => 'Registered successfully', 'user' => $user->getEmail()], 201);
    }
}
