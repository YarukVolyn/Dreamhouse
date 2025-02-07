<?php

namespace App\Form;

use App\Entity\ContactUsRequest;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\TextEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactUsType extends AbstractType
{
    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => $this->translator->trans('Name'),
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'attr' => [
                    'placeholder' => $this->translator->trans('Name'),
                ],
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => $this->translator->trans('Email'),
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'attr' => [
                    'placeholder' => $this->translator->trans('Email'),
                ],
                'required' => true,
            ])
            ->add('phone', TextType::class, [
                'label' => $this->translator->trans('Phone'),
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'attr' => [
                    'placeholder' => $this->translator->trans('Phone'),
                ],
                'required' => false,
            ])
            ->add('message', TextareaType::class, [
                'label' => $this->translator->trans('Message'),
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'attr' => [
                    'rows' => 4,
                    'placeholder' => $this->translator->trans('Message'),
                ],
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactUsRequest::class,
        ]);
    }
}
