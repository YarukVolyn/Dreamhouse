<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class UserCrudController extends AbstractCrudController
{
    protected TranslatorInterface $translator;

    private EmailVerifier $emailVerifier;

    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(EmailVerifier $emailVerifier, TranslatorInterface $translator, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->emailVerifier = $emailVerifier;
        $this->translator = $translator;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field::new('id')->hideOnForm(),
            Field::new('email')->setLabel($this->translator->trans('Email')),
            TextField::new('password')->setFormType(PasswordType::class)->onlyOnForms()->setLabel($this->translator->trans('Password')),
            ChoiceField::new('roles')->setChoices(['ROLE_USER' => 'ROLE_USER', 'ROLE_ADMIN' => 'ROLE_ADMIN'])->allowMultipleChoices()->setLabel($this->translator->trans('Roles')),
            Field::new('isVerified')->onlyOnDetail()->setLabel($this->translator->trans('Is Verified')),
        ];
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setPassword(
            $this->userPasswordHasher->hashPassword(
                $entityInstance,
                $entityInstance->getPassword()
            )
        );

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setPassword(
            $this->userPasswordHasher->hashPassword(
                $entityInstance,
                $entityInstance->getPassword()
            )
        );

        $entityManager->persist($entityInstance);
        $entityManager->flush();

        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $entityInstance,
            (new TemplatedEmail())
                ->from(new Address('dreamhouse.lutsk@gmail.com', $this->translator->trans('Dream House Real Estate Agency')))
                ->to($entityInstance->getEmail())
                ->subject($this->translator->trans('Please Confirm your Email'))
                ->htmlTemplate('user/registration/confirmation_email.html.twig')
        );
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural($this->translator->trans('Users'))
            ->setEntityLabelInSingular($this->translator->trans('User'))
        ;
    }
}
