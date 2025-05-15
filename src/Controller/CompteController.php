<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CompteController extends AbstractController
{
    #[Route('/compte', name: 'app_compte')]
    public function index(ReservationRepository $reservationRepo): Response
    {
        // Vérifie que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Récupère les réservations de l'utilisateur
        $reservations = $reservationRepo->findByUser($this->getUser());

        return $this->render('compte/index.html.twig', [
            'reservations' => $reservations
        ]);
    }
}
