<?php

namespace App\Controller\Admin;

use App\Entity\Template;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TemplateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Template::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('title', 'Titre');
        yield TextField::new('description', 'Description');
        yield AssociationField::new('documentType', 'Type de document');
        yield AssociationField::new('country', 'Pays');
        yield CodeEditorField::new('content', 'Modèle (Twig/HTML)')
            ->setLanguage('html')
            ->setNumOfRows(20)
            ->setHelp('Utilisez la syntaxe Twig. Ex: {{ name }}, {{ date }}');
    }
}
