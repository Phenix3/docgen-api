<?php

namespace App\Controller\Admin;

use App\Entity\DocumentType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DocumentTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DocumentType::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nom');
        yield TextField::new('slug', 'Slug');
        yield TextField::new('category', 'Catégorie');
        yield TextField::new('description', 'Description');
    }
}
