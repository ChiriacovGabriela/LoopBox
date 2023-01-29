<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreatePlaylistController extends AbstractController
{
    #[Route('/create/playlist', name: 'app_create_playlist')]
    public function index(): Response
    {
        return $this->render('create_playlist/index.html.twig', [
            'controller_name' => 'CreatePlaylistController',
        ]);
    }
}
