<?php

namespace App\Controller;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;  // Assure-toi d'importer ce namespace
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    private $entityManager;

    // Injection de la dépendance EntityManagerInterface via le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/user/dashboard', name: 'app_user_dashboard')]

     
    public function index(): Response
    {
        // Vérifie que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // Récupère l'utilisateur connecté
        $user = $this->getUser();
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // Utilise l'EntityManager pour récupérer les événements
        $events = $this->entityManager->getRepository(Event::class)->findAll();

        return $this->render('home/index.html.twig', [
            'user' => $user,
            'events' => $events, // Passe les événements à la vue
        ]);
    }
}