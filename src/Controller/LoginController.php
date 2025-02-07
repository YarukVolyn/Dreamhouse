<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\SiteHelperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginController extends AbstractController
{
    protected SiteHelperService $siteHelperService;

    protected TranslatorInterface $translator;

    public function __construct(SiteHelperService $site_helper_service, TranslatorInterface $translator)
    {
        $this->siteHelperService = $site_helper_service;
        $this->translator = $translator;
    }

    /**
     * @Route("/{_locale<en|uk>}/user/login", name="app_login")
     */
    public function index(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isGranted('IS_ANONYMOUS')) {
            $parameters = $this->siteHelperService->getBaseParameters($request);
            $parameters['page_title'] = $this->translator->trans('Login');
            $error = $authenticationUtils->getLastAuthenticationError();
            $parameters['error'] = $error;
            $lastUsername = $authenticationUtils->getLastUsername();
            $parameters['last_username'] = $lastUsername;
            $parameters['csrf_token_intention'] = 'authenticate';
            $parameters['target_path_parameter'] = '_target_path';
            $parameters['target_path'] = $this->generateUrl('app_admin');
            $parameters['_username_label'] = $this->translator->trans('Username');
            $parameters['_password_label'] = $this->translator->trans('Password');
            $parameters['_sign_in_label'] = $this->translator->trans('Log in');
            $parameters['username_parameter'] = '_username';
            $parameters['password_parameter'] = '_password';
            $parameters['forgot_password_enabled'] = true;
            $parameters['forgot_password_path'] = $this->generateUrl('app_forgot_password_request');
            $parameters['_forgot_password_label'] = $this->translator->trans('Forgot your password?');
            $parameters['remember_me_enabled'] = true;
            $parameters['remember_me_parameter'] = '_remember_me';
            $parameters['remember_me_checked'] = false;
            $parameters['_remember_me_label'] = $this->translator->trans('Remember me');

            return $this->render('user/login/index.html.twig', $parameters);
        }

        return $this->redirectToRoute('app_homepage');
    }

    /**
     * @Route("/user/login")
     */
    public function indexNoLocale(): Response
    {
        return $this->redirectToRoute('app_login', ['_locale' => 'uk']);
    }
}
