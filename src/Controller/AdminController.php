<?php
namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/event', name: 'admin_event_index')]
    public function eventIndex(EventRepository $eventRepo): Response
    {
        return $this->render('admin/event/index.html.twig', [
            'event' => $eventRepo->findAll()
        ]);
    }

    #[Route('/event/nouveau', name: 'admin_event_new')]
    public function newEvent(Request $request, EntityManagerInterface $em): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $event->setOrganisateur($this->getUser());
            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'Événement créé avec succès');
            return $this->redirectToRoute('admin_event_index');
        }

        return $this->render('admin/event/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/evenements/editer/{id}', name: 'admin_event_edit')]
    public function editEvent(Event $event, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Événement mis à jour');
            return $this->redirectToRoute('admin_event_index');
        }

        return $this->render('admin/event/edit.html.twig', [
            'form' => $form->createView(),
            'event' => $event
        ]);
    }

    #[Route('/event/supprimer/{id}', name: 'admin_event_delete')]
    public function deleteEvent(Event $event, EntityManagerInterface $em): Response
    {
        $em->remove($event);
        $em->flush();
        $this->addFlash('success', 'Événement supprimé');
        return $this->redirectToRoute('admin_event_index');
    }
}
