<?php

namespace App\Controller\Admin;

use App\Entity\Faq;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\HttpFoundation\Response;

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
            TextField::new('question','Titre de la question'),
            TextEditorField::new('answer', 'Contenu de réponse')->onlyOnForms(),
            TextField::new('content', 'Contenu de réponse')->renderAsHtml()->hideOnForm()->hideOnIndex(),
            BooleanField::new('isVisible', 'Est visible')->renderAsSwitch(false)
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $disableFaq = function (Faq $faq) {
            return $faq->isIsVisible();
        };

        $enableFaq = function (Faq $faq) {
            return !$faq->isIsVisible();
        };

        $viewEnableAction = Action::new('enable', 'Rendre visible la question')
            ->setCssClass('btn')
            ->addCssClass('text-success')
            ->displayIf($enableFaq)
            ->displayAsLink()
            ->linkToCrudAction('enable')
            ;

        $viewDisableAction = Action::new('disable', 'Désactiver la question')
            ->setCssClass('btn')
            ->addCssClass('text-danger')
            ->displayIf($disableFaq)
            ->displayAsLink()
            ->linkToCrudAction('disable')
            ;

        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
        $actions->add(Crud::PAGE_DETAIL, $viewEnableAction);
        $actions->add(Crud::PAGE_INDEX, $viewEnableAction);

        $actions->add(Crud::PAGE_DETAIL, $viewDisableAction);
        $actions->add(Crud::PAGE_INDEX, $viewDisableAction);

       return $actions;
    }

    public function enable(AdminContext $context, AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $entityManager) : Response
    {
        return $this->updateFaq($context, $adminUrlGenerator, $entityManager);
    }

    public function disable(AdminContext $context, AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $entityManager) : Response
    {
      return $this->updateFaq($context, $adminUrlGenerator, $entityManager);
    }

    private function updateFaq(AdminContext             $context,
                               AdminUrlGenerator        $adminUrlGenerator,
                               EntityManagerInterface   $entityManager) : Response {
        $faq = $context->getEntity()->getInstance();

        if($faq->isIsVisible()) {
            $faq->setIsVisible(false);
            $this->addFlash('success', 'La FAQ a été désactivée avec succès.');
        } else {
            $faq->setIsVisible(true);
            $this->addFlash('success', 'La FAQ a été activée avec succès.');
        }

        $entityManager->persist($faq); $entityManager->flush();

        $url = $adminUrlGenerator
            ->setController(self::class)
            ->setAction(Crud::PAGE_INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }
}
