<?php

namespace App\Controller;

use App\Entity\Song;
use App\Form\SongFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SongController extends AbstractController
{
    #[Route('/song', name: 'app_song')]
    public function index(): Response
    {
        return $this->render('song/index.html.twig', [
            'controller_name' => 'SongController',
        ]);
    }


    #[Route('/song/add', name: 'add_song')]
    public function add(): Response
    {
        $song = new Song();
        $songForm = $this->createForm(SongFormType::class, $song);
        return $this->render('song/add.html.twig', [
            'songForm' => $songForm->createView()
        ]);
    }


}
