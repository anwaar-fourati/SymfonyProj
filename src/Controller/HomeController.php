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

    #[Route('/', name: 'app_home')]

     
    public function index(): Response
    {
        
        $events = $this->entityManager->getRepository(Event::class)->findAll();

        return $this->render('home/index.html.twig', [
            
            'events' => $events, // Passe les événements à la vue
        ]);
    }
}