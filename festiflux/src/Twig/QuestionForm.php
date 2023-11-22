<?php


namespace App\Twig;

use App\Entity\QuestionBenevole;
use App\Form\QuestionBenevoleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent]
class QuestionForm extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    #[LiveProp(fieldName: 'formData')]
    public ?QuestionBenevole $questions;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(
            QuestionBenevoleType::class,
            $this->questions
        );
    }
}
