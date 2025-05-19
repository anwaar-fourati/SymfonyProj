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

    #[Route('/reserver/{id}', name: 'event_reserver')]
    public function reserver(Request $request, Event $event, EntityManagerInterface $em): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);

        // Pré-remplir les données
        $reservation->setEvent($event);
        $reservation->setUtilisateur($this->getUser());
        $reservation->setDateReservation(new \DateTime());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reservation);
            $em->flush();

            return $this->redirectToRoute('reservation_confirmation', [
                'id' => $reservation->getId()
            ]);
        }

        return $this->render('reservation/new.html.twig', [
            'form' => $form->createView(),
            'event' => $event
        ]);
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