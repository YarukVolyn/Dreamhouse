<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\SiteHelperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomepageController extends AbstractController
{
    protected SiteHelperService $siteHelperService;

    protected TranslatorInterface $translator;

    public function __construct(SiteHelperService $site_helper_service, TranslatorInterface $translator)
    {
        $this->siteHelperService = $site_helper_service;
        $this->translator = $translator;
    }

    /**
     * @Route("/{_locale<en|uk>}", name="app_homepage")
     */
    public function index(Request $request): Response
    {
        $hello_text = $this->translator->trans('hello');
        $parameters = $this->siteHelperService->getBaseParameters($request);
        $parameters['page_title'] = $this->translator->trans('Home');
        $parameters['page'] = [
            'breadcrumb' => [
                'home' => [
                    'active' => true,
                    'href' => '/'.$this->translator->getLocale().'/',
                    'title' => $this->translator->trans('Home'),
                ],
            ],
            'content' => [
                'title' => $this->translator->trans('Home'),
                'text' => $hello_text,
            ],
        ];

        return $this->render('content/homepage/index.html.twig', $parameters);
    }

    /**
     * @Route("/")
     */
    public function indexNoLocale(): Response
    {
        return $this->redirectToRoute('app_homepage', ['_locale' => 'uk']);
    }
}
