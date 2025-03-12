<?php

namespace App\Infrastructure\Controller\User;

use App\Domain\User\Entity\User;
use App\Domain\User\Service\PreferenceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PreferenceController extends AbstractController
{
    public function __construct(private PreferenceService $preferenceService) {}

    #[Route('/profile/preferences/get', name: 'profile_preferences_get', methods: ['GET'])]
    public function get(Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        if ($user) {
            // Если пользователь авторизован, берём тему из preferences
            $preferences = $user->getPreferences();
            $theme = $preferences['theme'] ?? 'light';
        } else {
            // Если не авторизован, берём из куки
            $theme = $request->cookies->get('theme', 'light');
        }

        return $this->json(['theme' => $theme]);
    }

    #[Route('/profile/preferences/set', name: 'profile_preferences_set', methods: ['POST'])]
    public function set(Request $request, #[CurrentUser] ?User $user): Response
    {
        $data = json_decode($request->getContent(), true);
        $theme = $data['theme'] ?? null;

        if (!in_array($theme, ['light', 'dark'])) {
            return new Response('Invalid theme', 400);
        }

        $response = new Response('Theme updated');

        if ($user) {
            // Если пользователь авторизован, сохраняем в preferences
            $this->preferenceService->setPreference($user, 'theme', $theme);
        } else {
            // Если не авторизован, сохраняем только в куки
            $response->headers->setCookie(new Cookie('theme', $theme, strtotime('+1 year')));
        }

        return $response;
    }
}
