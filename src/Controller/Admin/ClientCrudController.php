<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_USER")
 */
class ClientCrudController extends AbstractCrudController
{
    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getEntityFqcn(): string
    {
        return Client::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural($this->translator->trans('Clients'))
            ->setEntityLabelInSingular($this->translator->trans('Client'))
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field::new('id')
                ->hideOnForm(),
            Field::new('date')->setLabel($this->translator->trans('Date')),
            Field::new('name')->setRequired(true)->setLabel($this->translator->trans('Name')),
            TelephoneField::new('phone')->setRequired(true)->setLabel($this->translator->trans('Phone')),
            Field::new('client_request')->setRequired(true)->setLabel($this->translator->trans('Client request')),
            ChoiceField::new('status')
                ->setChoices([
                    $this->translator->trans('Active') => 'active',
                    $this->translator->trans('Nonactive') => 'nonactive',
                    $this->translator->trans('Closed') => 'closed',
                ])
                ->setLabel($this->translator->trans('Client status')),
        ];
    }
}
