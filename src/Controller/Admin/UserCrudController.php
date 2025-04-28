<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;


class UserCrudController extends AbstractCrudController
{
    private $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    


    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }
    
    public static function getEntityFqcn(): string
    {
        return User::class;
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setIcon('plus')->setLabel(false);
            });
    }
    public function configureFields(string $pageName): iterable
    {
        $fields = [
            TextField::new('email'),
            TextField::new('name'),
            AssociationField::new('company_id')
            ->setRequired(true)
            ->setLabel('Company'),
        ];

        // Show roles as choices with multiple selection
        $rolesField = ChoiceField::new('roles')
        ->setChoices([
            'User' => 'ROLE_USER',
            'Admin' => 'ROLE_ADMIN',
            'Guest' => 'ROLE_GUEST',
        ])
        ->allowMultipleChoices();

        $fields[] = $rolesField;
        if ($pageName === Crud::PAGE_NEW || $pageName === Crud::PAGE_EDIT) {
            $fields[] = TextField::new('password')
                ->setFormType(PasswordType::class)
                ->setLabel('Password')
                ->onlyOnForms();
        }

        return $fields;
    }


    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            // Check if email is unique
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $entityInstance->getEmail()]);
            
            if ($existingUser) {
                // Add flash message
                $this->addFlash('danger', '<i class="fa-solid fa-circle-exclamation text-danger"></i> The email is already in use.');
                return; // Exit without persisting
            }
            
            if (!empty($entityInstance->getPassword())) {
                $entityInstance->setPassword(
                    $this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPassword())
                );
            }
        }
        $this->addFlash('success', '<i class="fa-solid fa-circle-check text-success"></i> The user is created successfully.');
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            // Check if email is unique, excluding the current user
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $entityInstance->getEmail()]);
            
            if ($existingUser && $existingUser->getId() !== $entityInstance->getId()) {
                // Add flash message
                $this->addFlash('danger', '<i class="fa-solid fa-circle-exclamation text-danger"></i> The email is already in use.');
                return; // Exit without updating
            }
            
            if (!empty($entityInstance->getPassword())) {
                $entityInstance->setPassword(
                    $this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPassword())
                );
            }
        }
        $this->addFlash('success', '<i class="fa-solid fa-circle-check text-success"></i> The user is updated successfully.');
        parent::updateEntity($entityManager, $entityInstance);
    }
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $TelefonBox = $entityInstance->getTelefonBoxes();
        $count = count($TelefonBox);
        if (!$entityInstance instanceof User) return;

        if ($count > 0) {
            $this->addFlash(
                'danger',
                sprintf(
                    '<i class="fa-solid fa-circle-exclamation text-danger"></i>
                    Cannot delete User "%s" because it is used by %d Reserve record(s).',
                    $entityInstance->getName(),
                    $count
                )
            );
            return;
        }

        parent::deleteEntity($entityManager, $entityInstance);

        $this->addFlash('success', '<i class="fa-solid fa-circle-check text-success"></i> User deleted!');
    }

}