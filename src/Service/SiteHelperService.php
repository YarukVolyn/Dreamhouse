<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SiteHelperService
{
    protected TranslatorInterface $translator;
    protected TokenStorageInterface $token;
    protected ManagerRegistry $managerRegistry;
    protected RouterInterface $router;

    public function __construct(TranslatorInterface $translator, TokenStorageInterface $token, ManagerRegistry $managerRegistry, RouterInterface $router)
    {
        $this->translator = $translator;
        $this->token = $token;
        $this->managerRegistry = $managerRegistry;
        $this->router = $router;
    }

    public function getBaseParameters(Request $request): array
    {
        $lang = $request->getLocale();
        $data = $this->managerRegistry->getConnection()->fetchAssociative('SELECT * FROM site_settings WHERE id = ?', [1]);
        if ($data) {
            $textDirection = $data['text_direction'];
            $favicon = $data['favicon_url'];
            $logo = $data['logo_url'];
            $footer_logo = $data['footer_logo_url'];
            $copyright = $data['copyright_text'];
        } else {
            $textDirection = 'ltr';
            $favicon = '/images/logo.svg';
            $logo = '/images/logo-white.svg';
            $footer_logo = '/images/logo-white.svg';
            $copyright = $this->translator->trans('© 2022 Dream House Real Estate Agency');
        }
        $css_assets = $this->getCssAssets();
        $js_assets = $this->getJsAssets();
        $head_contents = $this->getHeadContents();
        $main_menu = $this->getMenu('main');
        $social_menu = $this->getMenu('social');
        $token = $this->token->getToken();
        if ($token) {
            $user = $token->getUser();
        } else {
            $user = [];
        }

        return [
            'lang' => $lang,
            'textDirection' => $textDirection,
            'favicon' => $favicon,
            'css_assets' => $css_assets,
            'js_assets' => $js_assets,
            'headContents' => $head_contents,
            'logo' => $logo,
            'footer_logo' => $footer_logo,
            'main_menu' => $main_menu,
            'copyright' => $copyright,
            'social_menu' => $social_menu,
            'user' => $user,
        ];
    }

    public function getMenu(string $id): array
    {
        if ($id === 'main') {
            $menu_attributes = [
                'class' => 'nav col-12 col-md-auto mb-2 justify-content-center mb-md-0',
            ];
            $menu_items = [
                0 => [
                    'title' => $this->translator->trans('Home'),
                    'url' => $this->router->generate('app_homepage'),
                    'is_expanded' => false,
                    'is_collapsed' => false,
                    'in_active_trail' => true,
                    'below' => [],
                ],
                3 => [
                    'title' => $this->translator->trans('About us'),
                    'url' => $this->router->generate('app_about_us'),
                    'is_expanded' => false,
                    'is_collapsed' => false,
                    'in_active_trail' => false,
                    'below' => [],
                ],
                4 => [
                    'title' => $this->translator->trans('Contact us'),
                    'url' => $this->router->generate('app_contact_us'),
                    'is_expanded' => false,
                    'is_collapsed' => false,
                    'in_active_trail' => false,
                    'below' => [],
                ],
            ];
            $menu = [
                'menu_attributes' => $menu_attributes,
                'menu_items' => $menu_items,
            ];
        } elseif ($id === 'social') {
            $menu_attributes = [
                'class' => 'nav col-md-4 justify-content-end d-flex',
            ];
            $menu_items = [
                0 => [
                    'title' => $this->translator->trans('Email'),
                    'url' => 'mailto:dreamhouse.lutsk@gmail.com',
                ],
                1 => [
                    'title' => $this->translator->trans('Facebook'),
                    'url' => 'https://www.facebook.com/DreamHouseLutsk',
                ],
                2 => [
                    'title' => $this->translator->trans('ASNU site'),
                    'url' => 'dreamhouse.asnu.net',
                ],
            ];
            $menu = [
                'menu_attributes' => $menu_attributes,
                'menu_items' => $menu_items,
            ];
        } else {
            $menu = [];
        }

        return $menu;
    }

//    public function getBreadcrumbs(): array
//    {
//        return [];
//    }

    /**
     * Return css assets for site.
     */
    public function getCssAssets(): array
    {
//        $css_assets_example = [
//            0 => [
//                'preload' => false,
//                'value' => '',
//                'htmlAttributes' => [
//                    // 'attr' =>  'value'
//                ],
//            ],
//        ];
        $css_flag = [
            'css_flag' => [
                'preload' => false,
                'value' => 'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css',
                'htmlAttributes' => [],
            ],
        ];

        return array_merge($css_flag);
    }

    /**
     * Return js assets for site.
     */
    public function getJsAssets(): array
    {
//        $js_assets_example = [
//            0 => [
//                'preload' => false,
//                'async' => false,
//                'defer' => false,
//                'value' => '',
//                'htmlAttributes' => [
//                    // 'attr' =>  'value'
//                ],
//            ],
//        ];

        return [];
    }

    /**
     * Return custom head contents for site.
     */
    public function getHeadContents(): array
    {
        return [];
    }
}
