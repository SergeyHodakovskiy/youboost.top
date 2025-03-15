<?php

namespace App\Infrastructure\Service;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class MenuService
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator
    ) {}

    public function getMenu(): array
    {
        $menu = [
            ['title' => $this->translator->trans('menu.home'), 'route' => 'home', 'icon' => 'fas fa-home'],
            ['title' => $this->translator->trans('menu.how_it_works'), 'route' => 'how_it_works', 'icon' => 'fas fa-question-circle'],
            ['title' => $this->translator->trans('menu.about'), 'route' => 'about', 'icon' => 'fas fa-info-circle'],
        ];

        if ($this->security->isGranted('ROLE_USER')) {
            $menu[] = ['title' => $this->translator->trans('menu.profile'), 'route' => 'user_profile', 'icon' => 'fas fa-user'];
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $menu[] = ['title' => $this->translator->trans('menu.admin'), 'route' => 'admin_dashboard', 'icon' => 'fas fa-cogs'];
        }

        return $menu;
    }
}
