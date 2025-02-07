<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ContactUsRequest;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_USER")
 */
class ContactUsRequestCrudController extends AbstractCrudController
{
    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getEntityFqcn(): string
    {
        return ContactUsRequest::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field::new('id')->hideOnForm(),
            Field::new('name')->setLabel($this->translator->trans('Name')),
            Field::new('email')->setLabel($this->translator->trans('Email')),
            TelephoneField::new('phone')->setRequired(false)->setLabel($this->translator->trans('Phone')),
            Field::new('message')->setLabel($this->translator->trans('Message')),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural($this->translator->trans('Contact us requests'))
            ->setEntityLabelInSingular($this->translator->trans('Contact us request'))
            ->setPageTitle(Crud::PAGE_DETAIL, $this->translator->trans('Contact us request # %entity_as_string%'))
        ;
    }
}
