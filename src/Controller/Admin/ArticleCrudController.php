<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_USER")
 */
class ArticleCrudController extends AbstractCrudController
{
    protected TranslatorInterface $translator;

    private ArticleRepository $articleRepository;

    public function __construct(ArticleRepository $articleRepository, TranslatorInterface $translator)
    {
        $this->articleRepository = $articleRepository;
        $this->translator = $translator;
    }

    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural($this->translator->trans('Articles'))
            ->setEntityLabelInSingular($this->translator->trans('Article'))
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $reviewArticle = Action::new('review', $this->translator->trans('Review'), 'fa fa-check')
            ->linkToCrudAction('reviewArticle')
        ;

        $publishArticle = Action::new('publish', $this->translator->trans('Publish'), 'fa fa-check-double')
            ->linkToCrudAction('publishArticle')
        ;

        $rejectArticle = Action::new('reject', $this->translator->trans('Reject'), 'fa fa-minus')
            ->linkToCrudAction('rejectArticle')
        ;

        $toDraftArticle = Action::new('toDraft', $this->translator->trans('To Draft'), 'fa')
            ->linkToCrudAction('toDraftArticle')
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, $reviewArticle)
            ->add(Crud::PAGE_EDIT, $publishArticle)
            ->add(Crud::PAGE_EDIT, $rejectArticle)
            ->add(Crud::PAGE_EDIT, $toDraftArticle)
            ->add(Crud::PAGE_DETAIL, $reviewArticle)
            ->add(Crud::PAGE_DETAIL, $publishArticle)
            ->add(Crud::PAGE_DETAIL, $rejectArticle)
            ->add(Crud::PAGE_DETAIL, $toDraftArticle)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field::new('id')
                ->hideOnForm(),
            Field::new('title')->setLabel($this->translator->trans('Title')),
            Field::new('content')->setLabel($this->translator->trans('Content')),
            AssociationField::new('image')->setCrudController(ImageCrudController::class)->setLabel($this->translator->trans('Image')),
            ChoiceField::new('current_place')->setChoices([
                $this->translator->trans('Draft') => 'draft',
                $this->translator->trans('Reviewed') => 'reviewed',
                $this->translator->trans('Rejected') => 'rejected',
                $this->translator->trans('Published') => 'published',
            ])->hideOnForm()->setLabel($this->translator->trans('Status')),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('current_place')->setChoices([
                $this->translator->trans('Draft') => 'draft',
                $this->translator->trans('Reviewed') => 'reviewed',
                $this->translator->trans('Rejected') => 'rejected',
                $this->translator->trans('Published') => 'published',
            ])->setLabel($this->translator->trans('Status')))
        ;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->articleRepository->draft($entityInstance);
        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function reviewArticle(): RedirectResponse
    {
        $entity = $this->getContext()->getEntity()->getInstance();
        $this->articleRepository->review($entity);
        $this->articleRepository->add($entity, true);
        $url = $this->getContext()->getReferrer()
            ?? $this->container->get(AdminUrlGenerator::class)->setAction(Action::INDEX)->generateUrl();

        return $this->redirect($url);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function rejectArticle(): RedirectResponse
    {
        $entity = $this->getContext()->getEntity()->getInstance();
        $this->articleRepository->rejected($entity);
        $this->articleRepository->add($entity, true);
        $url = $this->getContext()->getReferrer()
            ?? $this->container->get(AdminUrlGenerator::class)->setAction(Action::INDEX)->generateUrl();

        return $this->redirect($url);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function publishArticle(): RedirectResponse
    {
        $entity = $this->getContext()->getEntity()->getInstance();
        $this->articleRepository->publish($entity);
        $this->articleRepository->add($entity, true);
        $url = $this->getContext()->getReferrer()
            ?? $this->container->get(AdminUrlGenerator::class)->setAction(Action::INDEX)->generateUrl();

        return $this->redirect($url);

    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function toDraftArticle(): RedirectResponse
    {
        $entity = $this->getContext()->getEntity()->getInstance();
        $this->articleRepository->toDraft($entity);
        $this->articleRepository->add($entity, true);
        $url = $this->getContext()->getReferrer()
            ?? $this->container->get(AdminUrlGenerator::class)->setAction(Action::INDEX)->generateUrl();

        return $this->redirect($url);

    }
}
