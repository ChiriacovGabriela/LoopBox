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
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        //On crée un nouveau Playlist
        $playlist = new Playlist();
        //On crée le formulaire
        $playlistForm = $this->createForm(PlaylistFormType::class, $playlist);
        // On traite la requete du formulaire
        $playlistForm->handleRequest($request);

        // on verifie si le formulaire est soumis et valide
        if($playlistForm->isSubmitted() && $playlistForm->isValid()){
            //On stock
            $em-> persist($playlist);
            $em->flush();


            //On redirige
            return $this->redirectToRoute('app_playlist');

        }

        return $this->render('playlist/add.html.twig', [
            'playlistForm' => $playlistForm->createView()
        ]);
    }
    #[Route('/playlist/edit/{id}', name: 'edit')]
    public function edit (Playlist $playlist, Request $request, EntityManagerInterface $em ):Response
    {
        // ajouter la date pour update
        $playlist ->setUpdated_at(new \DateTimeImmutable());
        //On crée le formulaire
        $playlistForm = $this->createForm(PlaylistFormType::class, $playlist);
        // On traite la requete du formulaire
        $playlistForm->handleRequest($request);
        // on verifie si le formulaire est soumis et valide
        if($playlistForm->isSubmitted() && $playlistForm->isValid()){
            //On stock
            $em-> persist($playlist);
            $em->flush();


            //On redirige
            return $this->redirectToRoute('app_playlist');

        }
        return $this->render('playlist/edit.html.twig', [
            'playlistForm' => $playlistForm->createView(),
            'playlist' => $playlist
        ]);

    }



}
