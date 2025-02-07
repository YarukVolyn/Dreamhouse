<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChangePasswordFormType extends AbstractType
{
    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => $this->translator->trans('New password'),
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'placeholder' => $this->translator->trans('New password'),
                    ],
                    'row_attr' => [
                        'class' => 'form-floating',
                    ],
                    //                    'constraints' => [
                    //                        new NotBlank([
                    //                            'message' => 'Please enter a password',
                    //                        ]),
                    //                        new Length([
                    //                            'min' => 6,
                    //                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                    //                            // max length allowed by Symfony for security reasons
                    //                            'max' => 4096,
                    //                        ]),
                    //                    ],
                ],
                'second_options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'placeholder' => $this->translator->trans('Repeat password'),
                    ],
                    'label' => $this->translator->trans('Repeat password'),
                    'row_attr' => [
                        'class' => 'form-floating',
                    ],
                ],
                'invalid_message' => $this->translator->trans('The password fields must match.'),
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
