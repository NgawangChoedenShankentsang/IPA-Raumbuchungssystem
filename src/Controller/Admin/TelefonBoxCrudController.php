<?php
namespace App\Controller\Admin;

use App\Entity\TelefonBox;
use App\Entity\Status;
use App\Service\TelefonBoxNotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

#[AdminCrud(routePath: '/requests', routeName: 'requests')]
class TelefonBoxCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;
    private TelefonBoxNotificationService $notificationService;

    public function __construct(
        EntityManagerInterface $entityManager,
        TelefonBoxNotificationService $notificationService
    ) {
        $this->entityManager       = $entityManager;
        $this->notificationService = $notificationService;
    }

    public static function getEntityFqcn(): string
    {
        return TelefonBox::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Pending Requests')
            ->setPageTitle(Crud::PAGE_EDIT, 'Edit Request')
            ->setDefaultSort(['start_time' => 'DESC']);
    }

    /**
     * Only show TelefonBox rows where status_id = 1
     */
    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        // 1) get base QB
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        // 2) load the Status entity with ID=1
        $pendingStatus = $this->entityManager
                              ->getRepository(Status::class)
                              ->find(1);

        // 3) add WHERE status_id = :pending
        $rootAlias = $qb->getRootAliases()[0];
        $qb->andWhere(sprintf('%s.status_id = :pending', $rootAlias))
           ->setParameter('pending', $pendingStatus);

        return $qb;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title')
                ->onlyOnIndex(),
            AssociationField::new('status_id', 'Status')
                ->setTemplatePath('admin/badge.html.twig'),
            AssociationField::new('user_id', 'User')
                ->onlyOnIndex(),
            DateTimeField::new('start_time')
                ->onlyOnIndex(),
            DateTimeField::new('end_time')
                ->onlyOnIndex(),
            TextField::new('companyName', 'Company')
                ->onlyOnIndex(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->remove(Crud::PAGE_INDEX, Action::NEW);
    }
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof TelefonBox) return;

        parent::updateEntity($entityManager, $entityInstance);

        $this->addFlash('success', '<i class="fa-solid fa-circle-check text-success"></i> Request updated successfully!');
    }

}

