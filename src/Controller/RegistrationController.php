<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Service\SiteHelperService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    protected SiteHelperService $siteHelperService;

    protected TranslatorInterface $translator;

    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier, SiteHelperService $site_helper_service, TranslatorInterface $translator)
    {
        $this->emailVerifier = $emailVerifier;
        $this->siteHelperService = $site_helper_service;
        $this->translator = $translator;
    }

    /**
     * @Route("/{_locale<en|uk>}/user/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('IS_ANONYMOUS')) {
            $user = new User();
            $form = $this->createForm(RegistrationFormType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $entityManager->persist($user);
                $entityManager->flush();

                // generate a signed url and email it to the user
                $this->emailVerifier->sendEmailConfirmation(
                    'app_verify_email',
                    $user,
                    (new TemplatedEmail())
                        ->from(new Address('dreamhouse.lutsk@gmail.com', $this->translator->trans('Dream House Real Estate Agency')))
                        ->to($user->getEmail())
                        ->subject($this->translator->trans('Please Confirm your Email'))
                        ->htmlTemplate('user/registration/confirmation_email.html.twig')
                );

                // do anything else you need here, like send an email

                return $this->redirectToRoute('app_login');
            }

            $parameters = $this->siteHelperService->getBaseParameters($request);
            $parameters['page_title'] = $this->translator->trans('Register');
            $parameters['csrf_token_intention'] = true;
            $parameters['target_path'] = $this->generateUrl('app_homepage');
            $parameters['_username_label'] = $this->translator->trans('Username');
            $parameters['_password_label'] = $this->translator->trans('Password');
            $parameters['_sign_in_label'] = $this->translator->trans('Sign-up');
            $parameters['username_parameter'] = '_username';
            $parameters['password_parameter'] = '_password';
            $parameters['registrationForm'] = $form->createView();

            return $this->render('user/registration/register.html.twig', $parameters);
        }

        return $this->redirectToRoute('app_homepage');
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $user = $userRepository->find($request->get('id'));
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', $this->translator->trans('Your email address has been verified.'));

        return $this->redirectToRoute('app_homepage');
    }

    /**
     * @Route("/user/register")
     */
    public function indexNoLocale(): Response
    {
        return $this->redirectToRoute('app_register', ['_locale' => 'uk']);
    }
}
