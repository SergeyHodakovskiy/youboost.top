<?php

namespace App\Application\Security\Strategy;

use App\Domain\User\Entity\User;
use Symfony\Component\HttpFoundation\Request;

interface RegistrationStrategyInterface
{
    public function supports(Request $request): bool; // Проверяет, подходит ли стратегия для запроса
    public function register(Request $request): ?User; // Создаёт нового пользователя и возвращает его или null
}
