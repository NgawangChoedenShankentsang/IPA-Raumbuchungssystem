<?php

namespace App\Controller\Admin;

use App\Entity\Status;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class StatusCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Status::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Status Name'),
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
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Status) return;

        parent::persistEntity($entityManager, $entityInstance);

        $this->addFlash('success', 'New status was created successfully!');
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Status) return;

        parent::updateEntity($entityManager, $entityInstance);

        $this->addFlash('info', 'Status updated successfully!');
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $TelefonBox = $entityInstance->getTelefonBoxes();
        $count = count($TelefonBox);
        if (!$entityInstance instanceof Status) return;

        // Check if this company is linked to any users
        if ($count > 0) {
            $this->addFlash(
                'danger',
                sprintf(
                    'Cannot delete Status "%s" because it is used by %d Reserve record(s).',
                    $entityInstance->getName(),
                    $count
                )
            );
            return;
        }

        parent::deleteEntity($entityManager, $entityInstance);

        $this->addFlash('success', 'Status deleted!');
    }
    
}
