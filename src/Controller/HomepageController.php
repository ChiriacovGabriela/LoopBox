<?php

namespace App\Controller;

use App\Repository\SongRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/homepage', name: 'app_homepage')]
    public function index(SongRepository $songRepository): Response
    {
        return $this->render('homepage/index.html.twig', [
            'songs' => $songRepository->findAll(),
        ]);
    }
}
