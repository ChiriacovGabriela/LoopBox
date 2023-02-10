<?php

namespace App\Controller;

use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;




class UserController extends AbstractController
{
    #[Route('/user/{userId}', name: 'app_user')]
    public function index(PlaylistRepository $playlistRepository): Response
    {

        return $this->render('user/index.html.twig', [
            'playlists' => $playlistRepository->findBy(['user'=>$this->getUser()]),

        ]);
    }

}
