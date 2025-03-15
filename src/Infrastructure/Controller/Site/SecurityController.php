<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Site;

use App\Application\Security\AuthProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Infrastructure\Controller\Site\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Application\Security\Service\RegistrationService;
use App\Application\Security\Service\AuthenticationService;


class SecurityController extends BaseController
{
    public function __construct(
        private readonly AuthenticationService $authenticationService,
        private readonly RegistrationService $registrationService
    ) {}

    #[Route('/login', name: 'app_login', methods: ['GET'])]
    public function loginPage(Request $request): Response
    {
        return $this->render('security/login.html.twig', [
            'provider' => AuthProvider::EMAIL_PASSWORD->value,
            'error' => null,
            'email' => null,
        ]);
    }

    #[Route('/login/{provider}', name: 'app_login_provider', methods: ['POST'])]
    public function login(Request $request, string $provider): Response
    {
        if ($this->getUser()) {
            return new RedirectResponse('/dashboard');
        }

        if ($request->isMethod('POST')) {
            return $this->handleLogin($request, $provider);
        }

        return new RedirectResponse('/login');
    }

    private function handleLogin(Request $request, string $provider): Response
    {
        try {
            $authProvider = AuthProvider::fromString($provider);
            $user = $this->authenticationService->authenticate($request, $authProvider);

            if (!$user) {
                return new JsonResponse(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
            }

            return new RedirectResponse('/dashboard');
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request): Response
    {
        if ($this->getUser()) {
            return new RedirectResponse('/dashboard');
        }

        if ($request->isMethod('POST')) {
            return $this->handleRegistration($request);
        }

        return $this->render('security/register.html.twig');
    }

    private function handleRegistration(Request $request): Response
    {
        try {
            $user = $this->registrationService->register($request,  'email-password');

            return new RedirectResponse('/login/email-password');
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        $this->logoutUser();
    }

    #[Route('/reset-password', name: 'app_reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(Request $request): Response
    {
        return $this->render('security/reset_password.html.twig');
    }

    private function handleResetPassword(Request $request): Response
    {
        return new RedirectResponse('/login/email-password');
    }

    private function logoutUser(): void
    {
        $this->container->get('security.token_storage')->setToken(null);
    }
}
