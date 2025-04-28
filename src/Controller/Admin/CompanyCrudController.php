<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class CompanyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Company::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('company_name', 'Company Name'),
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
        if (!$entityInstance instanceof Company) return;

        parent::persistEntity($entityManager, $entityInstance);

        $this->addFlash('success', '<i class="fa-solid fa-circle-check text-success"></i> New Company was created successfully!');
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Company) return;

        parent::updateEntity($entityManager, $entityInstance);

        $this->addFlash('success', '<i class="fa-solid fa-circle-check text-success"></i> Company updated successfully!');
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $users = $entityInstance->getUsers();
        $count = count($users);
        if (!$entityInstance instanceof Company) return;

        // Check if this company is linked to any users
        if ($count > 0) {
            $this->addFlash(
                'danger',
                sprintf(
                    '<i class="fa-solid fa-circle-exclamation text-danger"></i>
                    Cannot delete Company "%s" because it is used by %d user record(s).',
                    $entityInstance->getCompanyName(),
                    $count
                )
            );
            return;
        }

        parent::deleteEntity($entityManager, $entityInstance);

        $this->addFlash('success', '<i class="fa-solid fa-circle-check text-success"></i> Company deleted!');
    }
    
}
