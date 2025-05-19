<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà connecté, on le redirige selon son rôle
        //if ($this->getUser()) {
            //return $this->redirectToTargetRoute();
        //}

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Redirige l'utilisateur vers la bonne route selon son rôle
     */
    private function redirectToTargetRoute(): RedirectResponse
    {
        $user = $this->getUser();
        $roles = $user ? $user->getRoles() : [];

        if (in_array('ROLE_ADMIN', $roles)) {
            return $this->redirectToRoute('app_admin');
        }

        if (in_array('ROLE_ORGANIZATOR', $roles)) {
            return $this->redirectToRoute('organisateur_dashboard');
        }

        // Par défaut pour ROLE_USER
        return $this->redirectToRoute('app_user_dashboard');
    }
}