<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\SiteSettingsFormType;
use App\Service\SiteHelperService;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SiteSettingsController extends AbstractController
{
    protected SiteHelperService $siteHelperService;

    protected TranslatorInterface $translator;

    public function __construct(SiteHelperService $site_helper_service, TranslatorInterface $translator)
    {
        $this->siteHelperService = $site_helper_service;
        $this->translator = $translator;
    }

    /**
     * @Route("/{_locale<en|uk>}/admin/settings", name="app_site_settings")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createForm(SiteSettingsFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data['id'] = 1;

            try {
                $select_data = $managerRegistry->getConnection()->fetchAssociative('SELECT * FROM site_settings WHERE id = ?', [1]);
                if ($select_data) {
                    $managerRegistry->getConnection()->update('site_settings', $data, ['id' => 1]);
                } else {
                    $managerRegistry->getConnection()->insert('site_settings', $data);
                }

                $request->getSession()->getFlashBag()->add('success', $this->translator->trans('Site settings updated successfully'));
            } catch (\Exception $e) {
                $request->getSession()->getFlashBag()->add('error', $this->translator->trans('There was an error while saving the site settings. ')
                    .$e->getMessage());
            }

            return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
        }

        $parameters['page_title'] = $this->translator->trans('Site Settings');
        $parameters['form'] = $form->createView();

        return $this->render('admin/site_settings/index.html.twig', $parameters);
    }

    /**
     * @Route("/admin/settings")
     */
    public function indexNoLocale(): Response
    {
        return $this->redirectToRoute('app_site_settings', ['_locale' => 'uk']);
    }
}
