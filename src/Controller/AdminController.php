<?php


namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $users = $userRepository->findAll();
        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/user/{id}/role', name: 'admin_user_role')]
    public function editUserRole(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $availableRoles = [
            'Utilisateur' => 'ROLE_USER',
            'Administrateur' => 'ROLE_ADMIN',
            'Organisateur' => 'ROLE_ORGANIZATOR',
        ];

        if ($request->isMethod('POST')) {
            $selectedRole = $request->request->get('role');
            
            // Validation supplémentaire
            if (!array_key_exists($selectedRole, array_flip($availableRoles))) {
                $this->addFlash('error', 'Rôle invalide');
                return $this->redirectToRoute('app_admin');
            }

            $user->setRoles([$selectedRole]);
            $em->flush();
            
            $this->addFlash('success', 'Rôle mis à jour avec succès');
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/edit_role.html.twig', [
            'user' => $user,
            'availableRoles' => $availableRoles,
            'currentRole' => $user->getRoles()[0] ?? 'ROLE_USER'
        ]);
    }

    #[Route('/admin/user/{id}/delete', name: 'admin_user_delete')]
    public function deleteUser(User $user, EntityManagerInterface $em): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        return $this->redirectToRoute('app_admin');
    }
}