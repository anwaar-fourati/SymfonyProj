<?php
// src/Controller/OrganisateurController.php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[Route('/organisateur')]
class OrganisateurController extends AbstractController
{
    #[Route('/organisateur/evenements', name: 'organisateur_events')]
public function index(EntityManagerInterface $em): Response
{
    $this->denyAccessUnlessGranted('ROLE_ORGANIZATOR');
    
    $events = $em->getRepository(Event::class)
                ->findBy(['organisateur' => $this->getUser()]);

    return $this->render('organisateur/index.html.twig', [
        'events' => $events,
    ]);
}

    #[Route('/evenement/nouveau', name: 'organisateur_event_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ORGANIZATOR');

        $event = new Event();
        $event->setOrganisateur($this->getUser());
        
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'Événement créé avec succès !');
            return $this->redirectToRoute('organisateur_events');
        }

        return $this->render('organisateur/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}