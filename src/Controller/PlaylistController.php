<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Form\PlaylistFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlaylistController extends AbstractController
{
    #[Route('/playlist', name: 'app_playlist')]
    public function index(): Response
    {
        return $this->render('playlist/index.html.twig', [
            'controller_name' => 'PlaylistController',
        ]);
    }

    #[Route('/playlist/add', name: 'add')]
    public function add(): Response
    {
        //On crée un nouveau Playlist
        $playlist = new Playlist();
        //On crée le formulaire
        $playlistForm = $this->createForm(PlaylistFormType::class, $playlist);
        return $this->render('playlist/add.html.twig', [
            'playlistForm' => $playlistForm->createView()
        ]);
    }

}
