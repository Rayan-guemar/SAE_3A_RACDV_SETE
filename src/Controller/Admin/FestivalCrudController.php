<?php

namespace App\Controller\Admin;

use App\Entity\Festival;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FestivalCrudController extends AbstractCrudController
{
    use Trait\ShowAndNewTrait;

    public static function getEntityFqcn(): string
    {

        return Festival::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setFormTypeOption('disabled','disabled'),
            TextField::new('nom'),
            DateField::new('date_debut'),
            DateField::new('date_fin'),
            TextField::new('description'),
            TextField::new('lieu'),
            TextField::new('affiche'),
            ];
        }

}
