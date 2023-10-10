<?php

namespace App\Service;

use App\Entity\Festival;
use App\Entity\Utilisateur;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;

class FlashMessageService {

    public function __construct(private RequestStack $requestStack, private ContainerInterface $container) {
    }

    public function getFlashbag(): mixed {
        try {
            $session = $this->container->get('request_stack')->getSession();
        } catch (SessionNotFoundException $e) {
            throw new \LogicException('You cannot use the FlashMessageService method if sessions are disabled. Enable them in "config/packages/framework.yaml".', 0, $e);
        }

        if (!$session instanceof FlashBagAwareSessionInterface) {
            trigger_deprecation('symfony/framework-bundle', '6.2', 'Calling "addFlash()" method when the session does not implement %s is deprecated.', FlashBagAwareSessionInterface::class);
        }

        return $session->getFlashBag();
    }

    public function addErrorsForm(FormInterface $form) {
        $errors = $form->getErrors(true);
        $flashBag = $this->getFlashbag();
        foreach ($errors as $error) {
            $flashBag->add(FlashMessageType::ERROR, $error->getMessage());
        }
    }

    public function add(FlashMessageType $type, string $message): void {
        $flashBag = $this->getFlashbag();
        $flashBag->add($type->value, $message);
    }
}
