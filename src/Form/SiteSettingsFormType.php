<?php

declare(strict_types=1);

namespace App\Form;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class SiteSettingsFormType extends AbstractType
{
    private TranslatorInterface $translator;

    private ManagerRegistry $managerRegistry;

    public function __construct(TranslatorInterface $translator, ManagerRegistry $managerRegistry)
    {
        $this->translator = $translator;
        $this->managerRegistry = $managerRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('text_direction', ChoiceType::class, [
                'label' => $this->translator->trans('Text direction'),
                'choices' => [
                    $this->translator->trans('Left-to-right text direction') => 'ltr',
                    $this->translator->trans('Right-to-left text direction') => 'rtl',
                    $this->translator->trans('Let the browser figure out the text direction, based on the content (only recommended if the text direction is unknown)') => 'auto',
                ],
            ])
            ->add('favicon_url', TextType::class, [
                'label' => $this->translator->trans('Favicon URL'),
                'required' => false,
            ])
            ->add('logo_url', TextType::class, [
                'label' => $this->translator->trans('Logo URL'),
                'required' => false,
            ])
            ->add('footer_logo_url', TextType::class, [
                'label' => $this->translator->trans('Footer logo URL'),
                'required' => false,
            ])
            ->add('copyright_text', TextareaType::class, [
                'label' => $this->translator->trans('Copyright text'),
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => $this->translator->trans('Submit'),
                'attr' => ['class' => 'w-100 btn btn-lg btn-primary'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $data = $this->managerRegistry->getConnection()->fetchAssociative('SELECT * FROM site_settings WHERE id = ?', [1]);
        if ($data) {
            $resolver->setDefaults([
                'data' => $data,
            ]);
        }
    }
}
