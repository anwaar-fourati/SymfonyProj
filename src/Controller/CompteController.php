<?php

namespace App\Controller;

use App\Repository\ReservationRepository; // Ajoutez cette ligne
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CompteController extends AbstractController
{
    #[Route('/compte', name: 'app_compte')]
    public function index(ReservationRepository $reservationRepository): Response // Correction du nom du paramètre
    {
        // Vérifie que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Récupère les réservations de l'utilisateur
        $reservations = $reservationRepository->findByUser($this->getUser());

        return $this->render('client/index.html.twig', [
            'reservations' => $reservations
        ]);
    }
}