<?php

namespace App\Controller;

use App\Service\SiteHelperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AboutUsController extends AbstractController
{
    protected SiteHelperService $siteHelperService;

    protected TranslatorInterface $translator;

    public function __construct(SiteHelperService $site_helper_service, TranslatorInterface $translator)
    {
        $this->siteHelperService = $site_helper_service;
        $this->translator = $translator;
    }

    /**
     * @Route("/{_locale<en|uk>}/about-us", name="app_about_us")
     */
    public function index(Request $request): Response
    {
        $text = $this->translator->trans('about_us');
        $parameters = $this->siteHelperService->getBaseParameters($request);
        $parameters['page_title'] = $this->translator->trans('About Us');
        $parameters['page'] = [
            'breadcrumb' => [
                'home' => [
                    'active' => false,
                    'href' => '/'.$this->translator->getLocale().'/',
                    'title' => $this->translator->trans('Home'),
                ],
                'about_us' => [
                    'active' => true,
                    'href' => '/'.$this->translator->getLocale().'/about-us',
                    'title' => $this->translator->trans('About us'),
                ],
            ],
            'content' => [
                'text' => $text,
            ],
        ];
        return $this->render('content/about_us/index.html.twig', $parameters);
    }

    /**
     * @Route("/about-us")
     */
    public function indexNoLocale(): Response
    {
        return $this->redirectToRoute('app_about_us', ['_locale' => 'uk']);
    }
}
