<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Site;

use App\Application\Security\AuthProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

abstract class BaseController extends AbstractController
{
    public function getAuthProviders(): array
    {
        return AuthProvider::cases();
    }

    #[AsEventListener(event: ControllerEvent::class)]
    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();
        $request->attributes->set('providers', $this->getAuthProviders());
    }
}
