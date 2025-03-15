<?php

namespace App\Application\Security\Service;

use App\Domain\User\Entity\User;

use Symfony\Component\HttpFoundation\Request;
use App\Application\Security\Strategy\Authentication\RegistrationStrategyInterface;

class RegistrationService
{
    /** @var iterable|RegistrationStrategyInterface[] */
    private iterable $strategies;

    public function __construct(iterable $strategies)
    {
        $this->strategies = $strategies;
    }

    public function register(Request $request, string $provider): User
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($provider)) {
                $user = $strategy->register($request);
                if ($user) {
                    return $user;
                }
            }
        }

        throw new \RuntimeException('No suitable registration strategy found');
    }
}
