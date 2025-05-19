<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;


class EventController extends AbstractController
{
    #[Route('/event', name: 'event_list')]
public function index(Request $request, EntityManagerInterface $entityManager): Response
{
    $events = $entityManager->getRepository(Event::class)->findAll();

    return $this->render('event/list.html.twig', [
        'events' => $events,
    ]);
}

#[Route('/event/{id}', name: 'event_show')]
public function show(int $id, Request $request, EntityManagerInterface $entityManager): Response
{
    $event = $entityManager->getRepository(Event::class)->find($id);

    if (!$event) {
        throw $this->createNotFoundException('Événement introuvable');
    }

    // Création du formulaire de réservation
    $reservationForm = $this->createFormBuilder()
        ->add('nombreDePlaces', IntegerType::class, [
            'label' => 'Nombre de places à réserver',
            'constraints' => [
                new NotBlank(),
                new GreaterThan(0), // Au moins 1 place à réserver
            ],
        ])
        ->getForm();

    $reservationForm->handleRequest($request);

    if ($reservationForm->isSubmitted() && $reservationForm->isValid()) {
        // Récupérer le nombre de places à réserver
        $nombreDePlaces = $reservationForm->getData()['nombreDePlaces'];

        // Vérifier qu'il y a suffisamment de places disponibles
        if ($event->getPlacesDisponibles() >= $nombreDePlaces) {
            // Réduire le nombre de places disponibles
            $event->setPlacesDisponibles($event->getPlacesDisponibles() - $nombreDePlaces);
            $entityManager->flush();

            // Message flash de succès
            $this->addFlash('success', 'Réservation effectuée avec succès !');

            // Redirection pour éviter un renvoi de formulaire
            return $this->redirectToRoute('event_show', ['id' => $id]);
        } else {
            // Si pas assez de places, ajouter un message d'erreur
            $this->addFlash('error', 'Il n\'y a pas assez de places disponibles pour cette réservation.');
        }
    }

    return $this->render('event/show.html.twig', [
        'event' => $event,
        'reservationForm' => $reservationForm->createView(),
    ]);
}



    #[Route('/event/{id}/edit', name: 'event_edit')]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = $entityManager->getRepository(Event::class)->find($id);
        
        if (!$event) {
            throw $this->createNotFoundException('Événement introuvable');
        }
        
        $eventForm = $this->createForm(EventType::class, $event);
        
        $eventForm->handleRequest($request);
        
        if ($eventForm->isSubmitted() && $eventForm->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'L\'événement a été modifié avec succès!');
            return $this->redirectToRoute('event_list');
        }
        
        return $this->render('event/index.html.twig', [
            'eventForm' => $eventForm->createView(),
            'event' => $event,
            'events' => $entityManager->getRepository(Event::class)->findAll(),
        ]);
    }

    #[Route('/event/{id}/delete', name: 'event_delete', methods: ['POST'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $event = $entityManager->getRepository(Event::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException("Événement introuvable.");
        }

        $entityManager->remove($event);
        $entityManager->flush();

        $this->addFlash('success', 'L\'événement a été supprimé avec succès.');

        return $this->redirectToRoute('event_list');
    }

    #[Route('/event/new', name: 'event_new')]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $event = new Event();
    $form = $this->createForm(EventType::class, $event);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($event);
        $entityManager->flush();

        $this->addFlash('success', 'Événement créé avec succès!');
        return $this->redirectToRoute('event_list');
    }

    return $this->render('event/new.html.twig', [
        'eventForm' => $form->createView(),
    ]);
}
}
