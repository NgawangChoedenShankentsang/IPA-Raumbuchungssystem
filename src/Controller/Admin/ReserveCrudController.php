<?php

namespace App\Controller\Admin;

use App\Entity\TelefonBox;
use App\Entity\Status;
use App\Service\TelefonBoxNotificationService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class ReserveCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;
    private TelefonBoxNotificationService $notificationService;
    public function __construct(EntityManagerInterface $entityManager, TelefonBoxNotificationService $notificationService)
    {
        $this->entityManager = $entityManager;
        $this->notificationService = $notificationService;
    }

    public static function getEntityFqcn(): string
    {
        return TelefonBox::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Reserve')
            ->setPageTitle(Crud::PAGE_EDIT, 'Edit Reserve')
            ->setPageTitle(Crud::PAGE_NEW, 'New Reserve');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            AssociationField::new('status_id', 'Status')
                ->hideOnForm()
                ->setTemplatePath('admin/badge.html.twig'),
            AssociationField::new('user_id', 'User')
                ->hideOnForm(),
            DateTimeField::new('start_time'),
            DateTimeField::new('end_time'),
            TextField::new('companyName', 'Company')
                ->onlyOnIndex()
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setIcon('plus')->setLabel(false);
            });
    }
    /**
     * Called when EasyAdmin creates a new entity instance.
     */
    public function createEntity(string $entityFqcn)
    {
        /** @var TelefonBox $telefonBox */
        $telefonBox = new $entityFqcn();
        // set the current user (from the Security token)
        $telefonBox->setUserId($this->getUser());

        // Fetch the Status entity with ID = 1
        $pendingStatus = $this->entityManager->getRepository(Status::class)->find(1);
        $telefonBox->setStatusId($pendingStatus);

        return $telefonBox;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof TelefonBox) {
            return;
        }

        $entityManager->persist($entityInstance);
        $entityManager->flush();

        $this->notificationService->notifyAdmins($entityInstance);

        // flash on create
        $this->addFlash(
            'success',
            '<i class="fa-solid fa-circle-check text-success"></i> 
             Reserve created successfully!'
        );
    }
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof TelefonBox) return;

        parent::updateEntity($entityManager, $entityInstance);

        $this->addFlash('success', '<i class="fa-solid fa-circle-check text-success"></i> Reserve updated successfully!');
    }
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof TelefonBox) {
            return;
        }

        // remove + flush
        $entityManager->remove($entityInstance);
        $entityManager->flush();

        // flash on delete
        $this->addFlash(
            'success',
            '<i class="fa-solid fa-circle-check text-success"></i> Reserve deleted successfully!'
        );
    }


}
