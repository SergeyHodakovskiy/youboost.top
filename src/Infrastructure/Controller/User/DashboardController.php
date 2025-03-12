<?php

namespace App\Infrastructure\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'user_dashboard')]
    public function index(): Response
    {
        return $this->render('user/dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    #[Route('/dashboard/preference', name: 'user_dashboard_preference')]
    public function preference(): Response
    {
        return $this->render('user/dashboard/preference.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }


    #[Route('/dashboard/preference/save', name: 'user_dashboard_preference_save')]
    public function savePreference(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');



        return $this->redirectToRoute('user_dashboard');
    }
}
