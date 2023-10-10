<?php

namespace App\Service;

use App\Entity\Festival;
use App\Entity\Utilisateur;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class FlashMessageService {

    public function __construct(private RequestStack $requestStack) {
    }

    public function getFlashbag(): mixed {
        return $this->requestStack->getSession()->getFlashBag();
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
