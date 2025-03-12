<?php

namespace App\Application\Security\Strategy;


use App\Domain\User\Entity\User;
use Symfony\Component\HttpFoundation\Request;

interface AuthenticationStrategyInterface
{
    public function supports(Request $request): bool; // Проверяет, подходит ли стратегия
    public function authenticate(Request $request): ?User; // Возвращает данные пользователя
}
