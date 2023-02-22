<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Entity\Song;
use App\Form\PlaylistFormType;
use App\Repository\AlbumRepository;
use App\Repository\PalylistRepository;
use App\Repository\PlaylistRepository;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\FormHandler\UploadFileHandler;
use App\Controller\UserController;
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
    #[Route('/playlist/{id}', name: 'app_playlist', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function index(SongRepository $songRepository, Playlist $playlist, PlaylistRepository $playlistRepository): Response
    {
        //dd($playlist);
        //dd($playlistRepository->find($playlist->getId())->getSongs()->toArray());
        return $this->render('playlist/index.html.twig', [
            'songs' => $songRepository->findAll(),
            'playlist' => $playlist,
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
        if ($playlistForm->isSubmitted() && $playlistForm->isValid()) {
            $imagePathFile = $playlistForm->get('imageFileName')->getData();
            if ($imagePathFile) {
                $directory = $this->getParameter('image_directory');
                $newFilename = $uploadFileHandler->upload($slugger, $imagePathFile, $directory);
                $playlist->setImageFileName($newFilename);
            }
            //On stock
            $em->persist($playlist);
            $em->flush();

            //On redirige
            $user = $this->getUser();
            $id = $user->getId();

            return $this->redirectToRoute('app_user', [
                'userId' => $id]);
        }
        return $this->render('playlist/add.html.twig', [
            'playlistForm' => $playlistForm->createView()
        ]);
    }

    #[Route('/playlist/edit/{id}', name: 'app_playlist_edit')]
    public function edit(Playlist $playlist, Request $request, EntityManagerInterface $em): Response
    {
        // ajouter la date pour update
        $playlist->setUpdated_at(new \DateTimeImmutable());

        //On crée le formulaire
        $playlistForm = $this->createForm(PlaylistFormType::class, $playlist);
        // On traite la requete du formulaire
        $playlistForm->handleRequest($request);
        // on verifie si le formulaire est soumis et valide
        if ($playlistForm->isSubmitted() && $playlistForm->isValid()) {
            //On stock
            //$em-> persist($playlist);
            $em->flush();
            //On redirige

            return $this->redirectToRoute('app_playlist', ['id' => $playlist->getId()]);

        }
        return $this->render('playlist/edit.html.twig', [
            'playlistForm' => $playlistForm->createView(),
            'playlist' => $playlist
        ]);
    }

    #[Route('/playlist/{playlistId}/song/{songId}', name: 'add_song_playlist')]
    #[Entity('playlist', options: ['id' => 'playlistId'])]
    #[Entity('song', options: ['id' => 'songId'])]
    public function addSongPlaylist(Playlist $playlistId, Song $songId, SongRepository $songRepository, PlaylistRepository $playlistRepository, EntityManagerInterface $em, Request $request): Response
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
    public function deleteSongPlaylist(Playlist $playlistId, Song $songId, SongRepository $songRepository, PlaylistRepository $playlistRepository, EntityManagerInterface $em, Request $request): Response
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

    #[Route('/playlist/{id}', name: 'app_playlist_delete', methods: ['POST'])]
    public function delete(Request $request, Playlist $playlist, PlaylistRepository $playlistRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $playlist->getId(), $request->request->get('_token'))) {
            $playlistRepository->remove($playlist, true);
        }

        $user = $this->getUser();
        $id = $user->getId();

        return $this->redirectToRoute('app_user', [
            'userId' => $this->getUser()->getId()]);
    }

    #[Route('/playlist/{playlistId}/song/{songId}/player', name: 'app_playlist_player')]
    #[Entity('playlist', options: ['id' => 'playlistId'])]
    #[Entity('song', options: ['id' => 'songId'])]
    public function player(Song $song, Playlist $playlist, PlaylistRepository $playlistRepository, Playlist $playlistId): Response
    {
        $playlistSongs = $playlist->getSongs()->toArray();

        $selectedSongKey = null;
        foreach ($playlistSongs as $key => $value) {
            if ($value->getId() === $song->getId()) {
                $selectedSongKey = $key;
            }
        }

        return $this->render('playlist/player.html.twig', [
            'song' => $song,
            'playlist' => $playlistRepository->find($playlistId),
            'next' => array_key_exists($selectedSongKey + 1, $playlistSongs) ? $playlistSongs[$selectedSongKey + 1] : null,
            'prev' => array_key_exists($selectedSongKey - 1, $playlistSongs) ? $playlistSongs[$selectedSongKey - 1] : null,
        ]);
    }


    #[Route('/song/{songId}/playlist/{playlistId}', name: 'add_song_homepage')]
    #[Entity('playlist', options: ['id' => 'playlistId'])]
    #[Entity('song', options: ['id' => 'songId'])]
    public function addSongHomepage(Playlist $playlistId, Song $songId, SongRepository $songRepository, PlaylistRepository $playlistRepository, EntityManagerInterface $em, AlbumRepository $albumRepository, Request $request): Response
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
        return $this->render('homepage/index.html.twig', [
            'playlist' => $playlistRepository->find($playlistId),
            'song' => $songId,
            'songs' => $songRepository->findAll(),
            'playlists' => $playlistRepository->findBy(['user' => $this->getUser()]),
            'albums' => $albumRepository->findAll(),
        ]);

    }

   }




