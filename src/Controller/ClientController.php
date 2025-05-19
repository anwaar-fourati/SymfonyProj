<?php

// src/Controller/ClientController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    #[Route('/client', name: 'app_client')]
    public function index(): Response
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
    $user = $this->getUser();
    
    if (!$user) {
        throw $this->createNotFoundException('Utilisateur non trouvé');
    }

    return $this->render('client/index.html.twig', [
        'user' => $user
    ]);
}
}