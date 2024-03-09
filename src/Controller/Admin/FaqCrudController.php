<?php

namespace App\Controller\Admin;

use App\Entity\Faq;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FaqCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Faq::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title','Titre de la question'),
            TextEditorField::new('content', 'Contenu de réponse')->onlyOnForms(),
            TextField::new('content', 'Contenu de réponse')->renderAsHtml()->hideOnForm()->hideOnIndex(),
            BooleanField::new('isVisible', 'Est visible')->renderAsSwitch(false)
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
       return $actions->add(Crud::PAGE_INDEX, Crud::PAGE_DETAIL);
    }


}
