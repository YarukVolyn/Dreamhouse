<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationFormType extends AbstractType
{
    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => $this->translator->trans('Email'),
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'attr' => [
                    'placeholder' => $this->translator->trans('Email'),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => $this->translator->trans('Agree terms'),
                //                'constraints' => [
                //                    new IsTrue([
                //                        'message' => 'You should agree to our terms.',
                //                    ]),
                //                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'label' => $this->translator->trans('Password'),
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => $this->translator->trans('Password'),
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                //                'constraints' => [
                //                    new NotBlank([
                //                        'message' => 'Please enter a password',
                //                    ]),
                //                    new Length([
                //                        'min' => 6,
                //                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                // max length allowed by Symfony for security reasons
                //                        'max' => 4096,
                //                    ]),
                //                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
