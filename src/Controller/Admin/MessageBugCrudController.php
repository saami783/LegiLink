<?php

namespace App\Controller\Admin;
use App\Entity\MessageContact;
use App\Enum\MessageStateEnum;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

class MessageBugCrudController extends AbstractCrudController
{

    public function __construct(private EntityManagerInterface $entityManager) { }

    public static function getEntityFqcn(): string
    {
        return MessageContact::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('email', 'E-mail'),
            TextField::new('message', 'Message')->hideOnForm(),
            ChoiceField::new('state', 'État du message')
                ->setChoices(array_combine(MessageStateEnum::getValues(), MessageStateEnum::getValues()))
                ->setRequired(true)
                ->hideOnIndex()
                ->hideOnDetail(),

            TextField::new('state', 'État du message')
                ->hideOnForm()
                ->formatValue(function ($value, $entity) {
                    $colorClass = MessageStateEnum::getColorClasses()[$value];
                    return sprintf('<span class="badge badge-%s">%s</span>', $colorClass, $value);
                })->renderAsHtml(),
            DateTimeField::new('sentAt', 'Envoyé le')->hideOnForm()
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Messages issus du formulaire de Bug');
    }


    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('id')
            ->add('email')
            ->add('sentAt')
            ->add(ChoiceFilter::new('state')
                ->setChoices(MessageStateEnum::getValues()))
            ;
    }


    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::DELETE)
            ->disable(Action::NEW)
            ;
    }

    /**
     * @param SearchDto $searchDto
     * @param EntityDto $entityDto
     * @param FieldCollection $fields
     * @param FilterCollection $filters
     * @return QueryBuilder
     */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->select('entity')
            ->from($entityDto->getFqcn(), 'entity')
            ->andWhere('entity.isBug = true');
    }

}
