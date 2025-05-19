<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Reservation;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(): Response
    {
        return $this->render('reservation/index.html.twig', [
            'controller_name' => 'ReservationController',
        ]);
    }

    #[Route('/event/{id}/reserve', name: 'event_reserve', methods: ['POST'])]
public function reserve(Event $event, Request $request, EntityManagerInterface $em): Response
{
    $this->denyAccessUnlessGranted('ROLE_USER');
    
    $user = $this->getUser();
    $places = $request->request->get('places', 1);

    // Validation
    if ($places <= 0 || $places > $event->getPlacesDisponibles()) {
        $this->addFlash('error', 'Nombre de places invalide');
        return $this->redirectToRoute('event_show', ['id' => $event->getId()]);
    }

    // Création de la réservation
    $reservation = new Reservation();
    $reservation->setEvent($event);
    $reservation->setUtilisateur($user);
    $reservation->setNombreDePlaces($places);
    $reservation->setDateReservation(new \DateTime());
    $reservation->setConfirme(true); // Ou false si besoin de confirmation

    // Mise à jour des places disponibles
    $event->setPlacesDisponibles($event->getPlacesDisponibles() - $places);

    // Sauvegarde
    $em->persist($reservation);
    $em->flush();

    $this->addFlash('success', 'Réservation confirmée !');
    return $this->redirectToRoute('user_reservations');
}

    #[Route('/mes-reservations', name: 'user_reservations')]
public function userReservations(ReservationRepository $reservationRepository): Response
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
    $user = $this->getUser();
    $reservations = $reservationRepository->findBy(['utilisateur' => $user]);

    return $this->render('reservation/user_reservations.html.twig', [
        'reservations' => $reservations,
    ]);
}
}