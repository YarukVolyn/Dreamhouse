<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Form\UserType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_USER")
 */
class MyProfileController extends AbstractController
{
    protected TranslatorInterface $translator;
    protected TokenStorageInterface $token;

    public function __construct(TranslatorInterface $translator, TokenStorageInterface $token)
    {
        $this->translator = $translator;
        $this->token = $token;
    }

    /**
     * @Route("/{_locale<en|uk>}/admin/profile", name="app_my_profile")
     */
    public function index(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $parameters['page_title'] = $this->translator->trans('My profile');
        $token = $this->token->getToken();
        if ($token) {
            $user = $token->getUser();
        } else {
            $user = [];
        }
        if ($user) {
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user->setPassword(
                    $this->userPasswordHasher->hashPassword(
                        $user,
                        $user->getPassword()
                    )
                );
                $userRepository->add($user, true);

                return $this->redirectToRoute('app_my_profile', [], Response::HTTP_SEE_OTHER);
            }

            $parameters['form'] = $form->createView();
        }

        return $this->render('admin/my_profile/index.html.twig', $parameters);
    }

    /**
     * @Route("/admin/profile")
     */
    public function indexNoLocale(): Response
    {
        return $this->redirectToRoute('app_my_profile', ['_locale' => 'uk']);
    }
}
