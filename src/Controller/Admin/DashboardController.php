<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Client;
use App\Entity\ContactUsRequest;
use App\Entity\Image;
use App\Entity\RealEstate;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_USER")
 */
class DashboardController extends AbstractDashboardController
{
    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/{_locale<en|uk>}/admin", name="app_admin")
     */
    public function index(): Response
    {
        if (!$this->getUser()->isVerified()) {
            throw new AccessDeniedException($this->translator->trans('Access Denied. User isn`t verified!'));
        }

        return $this->render('admin/admin_dashboard/index.html.twig', ['page_title' => $this->translator->trans('Dream House Admin Dashboard')]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img width="50px" height="50px" src="/images/logo-white.svg">')
            ->setFaviconPath('/images/logo.svg')
            ->setTranslationDomain('admin')
            ->setTextDirection('ltr')
            ->generateRelativeUrls()
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard($this->translator->trans('Dashboard'), 'fa fa-home');
        yield MenuItem::linkToRoute($this->translator->trans('Site Homepage'), 'fa fa-home', 'app_homepage');
        yield MenuItem::section($this->translator->trans('Users'))->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud($this->translator->trans('User list'), 'fa fa-user', User::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::section($this->translator->trans('Clients'))->setPermission('ROLE_USER');
        yield MenuItem::linkToCrud($this->translator->trans('Client list'), 'fa fa-address-card', Client::class)->setPermission('ROLE_USER');
        yield MenuItem::section($this->translator->trans('Real estates'))->setPermission('ROLE_USER');
        yield MenuItem::linkToCrud($this->translator->trans('Real estate list'), 'fa fa-building', RealEstate::class)->setPermission('ROLE_USER');
        yield MenuItem::section($this->translator->trans('Images'))->setPermission('ROLE_USER');
        yield MenuItem::linkToCrud($this->translator->trans('Image list'), 'fa fa-image', Image::class)->setPermission('ROLE_USER');
        yield MenuItem::section($this->translator->trans('Articles'))->setPermission('ROLE_USER');
        yield MenuItem::linkToCrud($this->translator->trans('Article list'), 'fa fa-newspaper', Article::class)->setPermission('ROLE_USER');
        yield MenuItem::section($this->translator->trans('Contact us requests'))->setPermission('ROLE_USER');
        yield MenuItem::linkToCrud($this->translator->trans('Contact us request list'), 'fa fa-inbox', ContactUsRequest::class)->setPermission('ROLE_USER');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->addMenuItems([
                MenuItem::linkToRoute($this->translator->trans('My Profile'), 'fa fa-id-card', 'app_my_profile'),
                MenuItem::linkToRoute($this->translator->trans('Settings'), 'fa fa-user-cog', 'app_site_settings'),
            ])
        ;
    }
}
