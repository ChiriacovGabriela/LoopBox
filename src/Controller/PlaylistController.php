<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Form\PlaylistFormType;
use App\Repository\PlaylistRepository;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\FormHandler\UploadFileHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PlaylistController extends AbstractController

{
    #[Route('/playlist/{id}', name: 'app_playlist', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function index(Playlist $playlist, SongRepository $songs ): Response
    {
        return $this->render('playlist/index.html.twig', [
            'playlist' => $playlist,
            'songs' => $songs->findAll(),
        ]);
    }

    #[Route('/playlist/add', name: 'app_playlist_add')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, UploadFileHandler $uploadFileHandler): Response
    {
        //On crée un nouveau Playlist
        $playlist = new Playlist();
        //On crée le formulaire
        $playlistForm = $this->createForm(PlaylistFormType::class, $playlist);
        $playlist->setUser($this->getUser());
        // On traite la requete du formulaire
        $playlistForm->handleRequest($request);
        // on verifie si le formulaire est soumis et valide
        if($playlistForm->isSubmitted() && $playlistForm->isValid()){
            $imagePathFile = $playlistForm ->get('imageFileName')->getData();
            if($imagePathFile){
                $directory = $this->getParameter('image_directory');
                $newFilename = $uploadFileHandler->upload($slugger,$imagePathFile,$directory);
                $playlist->setImageFileName($newFilename);
            }
            //On stock
            $em-> persist($playlist);
            $em->flush();

            //On redirige
            $user = $this->getUser();
            $id= $user->getId();

            return $this->redirectToRoute('app_user',[
                'userId' => $id]);
        }
        return $this->render('playlist/add.html.twig', [
            'playlistForm' => $playlistForm->createView()
        ]);
    }

    #[Route('/playlist/edit/{id}', name: 'app_playlist_edit')]
    public function edit (Playlist $playlist, Request $request, EntityManagerInterface $em ):Response
    {
        //On crée le formulaire
        $playlistForm = $this->createForm(PlaylistFormType::class, $playlist);
        // On traite la requete du formulaire
        $playlistForm->handleRequest($request);
        // on verifie si le formulaire est soumis et valide
        if($playlistForm->isSubmitted() && $playlistForm->isValid()){
            //On stock
            //$em-> persist($playlist);
            $em->flush();
            //On redirige
            return $this->redirectToRoute('app_playlist',[
                'id' => $playlist->getId()
            ]);

        }
        return $this->render('playlist/edit.html.twig', [
            'playlistForm' => $playlistForm->createView(),
            'playlist' => $playlist
        ]);
    }
    #[Route('/playlist/{id}', name: 'app_playlist_delete', methods: ['POST'])]
    public function delete(Request $request, Playlist $playlist, PlaylistRepository $playlistRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $playlist->getId(), $request->request->get('_token'))) {
            $playlistRepository->remove($playlist, true);
        }
        return $this->redirectToRoute('app_user', [
            'userId' => $this->getUser()->getId()]);
    }

}
