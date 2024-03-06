<?php

namespace App\Controller\Admin;

use App\Entity\Notification;
use App\Entity\NotificationUser;
use App\Entity\User;
use App\Enum\ProfessionEnum;
use App\Enum\ProfessionNotificationEnum;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class NotificationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Notification::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextareaField::new('message'),
            ChoiceField::new('category', 'Catégorie concernée')
                ->setChoices(ProfessionNotificationEnum::getValues())
                ->setRequired(true)
                ->onlyOnForms()
                ->setFormTypeOptions([
                    'expanded' => true,
                    'multiple' => false,
                    'choice_value' => function ($choice) {
                        return $choice;
                    },
                ])->onlyOnForms(),
            TextField::new('category')->hideOnForm(),
            DateField::new('createdAt', 'Envoyé le')->hideOnForm()->setFormat('dd.MM.yyyy hh:mm')->setTimezone('Europe/Paris'),
        ];
    }

    public function createEntity(string $entityFqcn): Notification
    {
        $notification = new Notification();
        $notification->setCreatedAt(new \DateTimeImmutable());

        return $notification;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Crud::PAGE_DETAIL);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Notifications');
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Notification $entityInstance */
        parent::persistEntity($entityManager, $entityInstance);

        $category = $entityInstance->getCategory();

        if ($category === ProfessionNotificationEnum::ALL) {
            $users = $entityManager->getRepository(User::class)->findAll();
        } else {
            $users = $entityManager->getRepository(User::class)->findBy(['profession' => $category]);
        }

        foreach ($users as $user) {
            $notificationUser = new NotificationUser();
            $notificationUser->setNotification($entityInstance);
            $notificationUser->setUser($user);
            $notificationUser->setRead(false);
            $entityManager->persist($notificationUser);
        }

        $entityManager->flush();
    }


}
