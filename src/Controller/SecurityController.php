<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SecurityFormType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @param SessionInterface $session
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils,
                          SessionInterface $session): Response
    {
        // Renvoi sur la route précédente ou la page d'accueil si l'utilisateur est déjà connécté.
        // Sauf si la dernière route visitée est app_login ou app_register (Event/RequestSubscriber.php).
        if ($this->getUser()) {
            if ($targetPath = $session->get('_security.main.target_path')) {
                return $this->redirect($targetPath);
            }

            return $this->redirectToRoute('post_index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error]
        );
    }

    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param GuardAuthenticatorHandler $guardHandler
     * @param AppAuthenticator $authenticator
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param SessionInterface $session
     * @return Response
     */
    public function register(Request $request,
                             GuardAuthenticatorHandler $guardHandler,
                             AppAuthenticator $authenticator,
                             EntityManagerInterface $manager,
                             UserPasswordEncoderInterface $encoder,
                             SessionInterface $session): Response
    {
        // Renvoi sur la page précédente ou la page d'accueil si l'utilisateur est déjà connécté.
        // Sauf si la dernière route visitée est app_login ou app_register (Event/RequestSubscriber.php).
        if ($this->getUser()) {
            if ($targetPath = $session->get('_security.main.target_path')) {
                return $this->redirect($targetPath);
            }

            return $this->redirectToRoute('post_index');
        }

        $user = new User();
        $form = $this->createForm(SecurityFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $encoder->encodePassword($user, $form->get('password')->getData())
            );

            $manager->persist($user);
            $manager->flush();

            // Connecte automatiquement l'utilisateur après la création de son compte
            // et le renvoi à la route précédente ou à la page d'accueil (Security/AppAuthenticator.php).
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user, $request, $authenticator, 'main' // firewall name in security.yaml
            );
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
    }
}
