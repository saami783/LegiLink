<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Enum\ProfessionEnum;
use App\Enum\UserRoleEnum;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;


class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom'),
            TextField::new('email'),
            TextField::new('profession')->hideOnForm(),
            ArrayField::new('views', 'Rôles')->hideOnForm(),

            ChoiceField::new('profession', 'Profession')
                ->setChoices(ProfessionEnum::getValues())
                ->setRequired(true)
                ->onlyOnForms()
                ->setFormTypeOptions([
                    'expanded' => true,
                    'multiple' => false,
                    'choice_value' => function ($choice) {
                        return $choice;
                    },
                ])->onlyOnForms(),
            ChoiceField::new('views', 'Rôles')
                ->setChoices(UserRoleEnum::getValues())
                ->setRequired(true)
                ->onlyOnForms()
                ->setFormTypeOptions([
                    'expanded' => true,
                    'multiple' => true,
                    'choice_value' => function ($choice) {
                        return $choice;
                    },
                ])->onlyOnForms(),

            BooleanField::new('isVerified', 'Utilisateur verifé')->renderAsSwitch(false),
            DateTimeField::new('createdAt', 'Date d\'inscription')->setTimezone('Europe/Paris')->hideOnForm()
        ];
    }

    /**
     *  Met en place une nouvelle action et active/désactive certaines actions.
     *  Deux actions "Activer le compte" sont instanciées pour les pages 'détail' et 'index'.
     *
     * L'édition, la suppression, la modification ainsi que l'activation de compte
     * sont possibles uniquement pour les utilisateurs SuperAdmin.
     *
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {

        $superAdmin = $this->getUser() instanceof UserInterface
            && in_array('ROLE_SUPER_ADMIN', $this->getUser()->getRoles());

        $actions->remove(Crud::PAGE_INDEX, Action::NEW);

        // J'active seulement le droit de lecture pour le rôle ADMIN.
        if (!$superAdmin) {
            $actions->remove(Crud::PAGE_INDEX, Action::DELETE);
            $actions->remove(Crud::PAGE_DETAIL, Action::DELETE);

            $actions->remove(Crud::PAGE_INDEX, Action::EDIT);
            $actions->remove(Crud::PAGE_DETAIL, Action::EDIT);
        }

        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);

        return $actions;
    }


    /**
     * Configure des filtres liés aux attributs de l'entité User
     *
     * @param Filters $filters L'instance filtre à configurer.
     *
     * @return Filters L'instance filtre configurée.
     */
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('id')
            ->add('name')
            ->add('isVerified')
            ->add('email')
            ->add(ChoiceFilter::new('profession')
                ->setChoices(ProfessionEnum::getValues()))
            ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Utilisateurs');
    }


}
