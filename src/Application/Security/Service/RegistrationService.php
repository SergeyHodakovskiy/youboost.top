<?php

namespace App\Application\Security\Service;

use App\Domain\User\Entity\User;
use App\Application\Security\Provider;
use Symfony\Component\HttpFoundation\Request;
use App\Application\Security\Strategy\RegistrationStrategyInterface;

class RegistrationService
{
    /** @var iterable|RegistrationStrategyInterface[] */
    private iterable $strategies;

    private Provider $provider;

    public function __construct(iterable $strategies)
    {
        $this->strategies = $strategies;
    }

    public function register(Request $request): User
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($request)) {
                $user = $strategy->register($request);
                if ($user) {
                    return $user;
                }
            }
        }

        throw new \RuntimeException('No suitable registration strategy found');
    }


    public function setProvider(Provider $provider): void
    {
        $this->provider = $provider;
    }
}
