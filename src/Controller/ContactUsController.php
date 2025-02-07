<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ContactUsRequest;
use App\Form\ContactUsType;
use App\Repository\ContactUsRequestRepository;
use App\Service\SiteHelperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactUsController extends AbstractController
{
    protected SiteHelperService $siteHelperService;

    protected TranslatorInterface $translator;

    public function __construct(SiteHelperService $site_helper_service, TranslatorInterface $translator)
    {
        $this->siteHelperService = $site_helper_service;
        $this->translator = $translator;
    }

    /**
     * @Route("/{_locale<en|uk>}/contact-us", name="app_contact_us")
     *
     * @throws TransportExceptionInterface
     */
    public function index(Request $request, ContactUsRequestRepository $contactUsRequestRepository, ChatterInterface $chatter): Response
    {
        $contactUsRequest = new ContactUsRequest();
        $form = $this->createForm(ContactUsType::class, $contactUsRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactUsRequestRepository->add($contactUsRequest, true);
            $request_message = $form->get('message')->getData();
            $message = (new ChatMessage($this->translator->trans('new_request_notifer').$request_message))->transport('telegram');
            $telegramOptionsAdmin = (new TelegramOptions())->chatId('400825833');
            $telegramOptionsDirector = (new TelegramOptions())->chatId('487256278');
            // Add telegram id admin and send notifier.
            $message->options($telegramOptionsAdmin);
            $chatter->send($message);
            // Add telegram id director and send notifier.
            $message->options($telegramOptionsDirector);
            $chatter->send($message);
            // @todo email notifer.
            return $this->redirectToRoute('app_contact_us', [], Response::HTTP_SEE_OTHER);
        }

        $parameters = $this->siteHelperService->getBaseParameters($request);
        $parameters['page_title'] = $this->translator->trans('Contact us');
        $parameters['csrf_token_intention'] = true;
        $parameters['target_path'] = $this->generateUrl('app_contact_us');
        $parameters['button_label'] = $this->translator->trans('Send');
        $parameters['contact_us_request'] = $contactUsRequest;
        $parameters['form'] = $form->createView();
        $parameters['help_text'] = $this->translator->trans('If you have questions, suggestions, ideas - fill out the form below and send us a list. We will be happy to answer all your questions in the near future.');
        $parameters['page'] = [
            'breadcrumb' => [
                'home' => [
                    'active' => false,
                    'href' => '/'.$this->translator->getLocale().'/',
                    'title' => $this->translator->trans('Home'),
                ],
                'contact_us' => [
                    'active' => true,
                    'href' => '/'.$this->translator->getLocale().'/contact-us',
                    'title' => $this->translator->trans('Contact us'),
                ],
            ],
        ];

        return $this->render('content/contact_us/index.html.twig', $parameters);
    }

    /**
     * @Route("/contact-us")
     */
    public function indexNoLocale(): Response
    {
        return $this->redirectToRoute('/contact/us', ['_locale' => 'uk']);
    }
}
