<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;

class AuthenticationSubscriber {
    public function __construct(private ContainerInterface $container) {
    }

    public function getFlashbag(): mixed {
        try {
            $session = $this->container->get('request_stack')->getSession();
        } catch (SessionNotFoundException $e) {
            throw new \LogicException('You cannot use the AuthenticationSubscriber method if sessions are disabled. Enable them in "config/packages/framework.yaml".', 0, $e);
        }

        if (!$session instanceof FlashBagAwareSessionInterface) {
            trigger_deprecation('symfony/framework-bundle', '6.2', 'Calling "addFlash()" method when the session does not implement %s is deprecated.', FlashBagAwareSessionInterface::class);
        }

        return $session->getFlashBag();
    }

    #[AsEventListener]
    public function onAuthenticationSuccess(LoginSuccessEvent $event): void {
        $this->getFlashBag()->add("success", "Vous êtes maintenant connecté.");
    }

    #[AsEventListener]
    public function onAuthenticationFailure(LoginFailureEvent $event): void {
        $this->getFlashBag()->add("error", "Identifiants incorrects.");
    }

    #[AsEventListener]
    public function onLogoutSuccess(LogoutEvent $event): void {
        $this->getFlashBag()->add("success", "Vous êtes maintenant déconnecté.");
    }
}
