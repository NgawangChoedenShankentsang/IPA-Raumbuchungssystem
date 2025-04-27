<?php

namespace App\Controller\Admin;

use App\Entity\TelefonBox;
use App\Entity\Status;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Doctrine\ORM\EntityManagerInterface;

class TelefonBoxCrudController extends AbstractCrudController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public static function getEntityFqcn(): string
    {
        return TelefonBox::class;
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
            // 👇 Add this field to show the company name of the assigned user
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
}
