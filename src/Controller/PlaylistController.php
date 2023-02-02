<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Entity\Song;
use App\Form\PlaylistFormType;
use App\Repository\PlaylistRepository;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PlaylistController extends AbstractController

{
    #[Route('/playlist/{id}', name: 'app_playlist', methods: ['GET'], requirements: ['id'=>'\d+'])]
    public function index(SongRepository $songRepository, Playlist $playlist, PlaylistRepository $playlistRepository): Response
    {
        //dd($playlist);
        //dd($playlistRepository->find($playlist->getId())->getSongs()->toArray());
        return $this->render('playlist/index.html.twig', [
            'songs' => $songRepository->findAll(),
            'playlist' => $playlistRepository->find($playlist)
        ]);
    }

    #[Route('/playlist/add', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        //On crée un nouveau Playlist
        $playlist = new Playlist();
        //On crée le formulaire
        $playlistForm = $this->createForm(PlaylistFormType::class, $playlist);
        // On traite la requete du formulaire
        $playlistForm->handleRequest($request);

        // on verifie si le formulaire est soumis et valide
        if($playlistForm->isSubmitted() && $playlistForm->isValid()){
            $imagePathFile = $playlistForm ->get('imageFileName')->getData();
            if($imagePathFile){
                $originalFilename = pathinfo($imagePathFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imagePathFile->guessExtension();
                try {
                    $imagePathFile->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    die ('File did not upload: ' . $e->getMessage());
                }
                $playlist->setImageFileName($newFilename);
            }
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
    #[Route('/playlist/edit/{id}', name: 'app_playlist_edit')]
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
            //$em-> persist($playlist);
            $em->flush();


            //On redirige
            return $this->redirectToRoute('app_playlist');

        }
        return $this->render('playlist/edit.html.twig', [
            'playlistForm' => $playlistForm->createView(),
            'playlist' => $playlist
        ]);

    }
    #[Route('/playlist/{playlistId}/song/{songId}', name: 'add_song_playlist')]
    #[Entity('playlist', options: ['id' => 'playlistId'])]
    #[Entity('song', options: ['id' => 'songId'])]
    public function addSongPlaylist(Playlist $playlistId, Song $songId, SongRepository $songRepository, PlaylistRepository $playlistRepository, EntityManagerInterface $em, Request $request):Response
    {

        // Check if the playlist and song exist
        if (!$playlistId || !$songId) {
            throw new NotFoundHttpException();
        }

        // Add the song to the playlist
        $playlistId->addSongPlaylist($songId);
        //dd($playlistId);

        // Persist the changes to the database
        $em->persist($playlistId);
        $em->flush();

        //dd($playlistId);
        return $this->render('playlist/index.html.twig', [
            'playlist' => $playlistRepository->find($playlistId),
            'song' => $songId,
            'songs' => $songRepository->findAll(),
        ]);
    }

    #[Route('/playlist/{playlistId}/dsong/{songId}', name: 'delete_song_playlist')]
    #[Entity('playlist', options: ['id' => 'playlistId'])]
    #[Entity('song', options: ['id' => 'songId'])]
    public function deleteSongPlaylist(Playlist $playlistId, Song $songId, SongRepository $songRepository, PlaylistRepository $playlistRepository, EntityManagerInterface $em, Request $request):Response
    {

        // Check if the playlist and song exist
        if (!$playlistId || !$songId) {
            throw new NotFoundHttpException();
        }

        // Add the song to the playlist
        $playlistId->removeSong($songId);
        //dd($playlistId);

        // Persist the changes to the database
        $em->persist($playlistId);
        $em->flush();

        //dd($playlistId);
        return $this->render('playlist/index.html.twig', [
            'playlist' => $playlistRepository->find($playlistId),
            'song' => $songId,
            'songs' => $songRepository->findAll(),
        ]);
    }



}