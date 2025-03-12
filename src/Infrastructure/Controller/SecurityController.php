<?php

namespace App\Infrastructure\Controller;

use App\Application\Security\Provider;
use App\Application\Security\Service\AuthenticationService;
use App\Application\Security\Service\RegistrationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{


    #[Route('/login', name: 'app_login', methods: ['POST', 'GET'])]
    public function login(Request $request, AuthenticationService $authenticationService): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }

        if ($request->isMethod('GET')) {
            return $this->render('security/login.html.twig',     [
                'error' => null,
                'last_username' => '',
            ]);
        }


        $request->request->set('provider', Provider::EMAIL_PASSWORD->value);

        $user = $authenticationService->authenticate($request);

        if (!$user) {
            return $this->render('security/login.html.twig',     [
                'error' => 'Неверный email или пароль',
                'last_username' => $request->request->get('_username', ''),
            ]);
            // return $this->json(['error' => 'Authentication failed'], 401);
        }
        return $this->redirectToRoute('user_dashboard');
        // return $this->json(['message' => 'Logged in successfully', 'user' => $user->getEmail()]);
    }

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(
        Request $request,
        RegistrationService $registrationService,
        ValidatorInterface $validator
    ): Response {
        // Здесь можно добавить валидацию DTO, если нужно
        $user = $registrationService->register($request);

        return $this->json(['message' => 'Registered successfully', 'user' => $user->getEmail()], 201);
    }
}
