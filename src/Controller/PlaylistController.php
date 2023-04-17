<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Entity\Song;
use App\Form\PlaylistFormType;
use App\Entity\Comment;
Use App\Form\CommentFormType;

use App\FormHandler\CommentHandler;

use App\Repository\CommentRepository;

use App\Repository\PlaylistRepository;
use App\Repository\SongRepository;
use App\FormHandler\UploadFileHandler;
use App\FormHandler\PlaylistFormHandler;

use Doctrine\ORM\EntityManagerInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PlaylistController extends AbstractController

{
    #[Route('/playlist/{id}', name: 'app_playlist', methods: ['GET'], requirements: ['id' => '\d+'])]

    public function index(Request $request,SongRepository $songRepository, Playlist $playlist): Response

    {
        $page = $request->query->getInt('page', 1);
        //on recupere les filtres
        $filters = $request->get('type');
        if ($filters != null) {
            //on recupere les bonnes chansons en fonction des filtres
            $songs = $songRepository->findSongsByType($filters);
            $filteredPlaylistSongs = $songRepository->findSongsByPlaylistAndType($playlist, $filters, $page, 5);
        } else {
            $songs = $songRepository->findAll();
            $filteredPlaylistSongs = $songRepository->findSongsByPlaylistPaginated($playlist, $page, 5);
        }
        $allSongs = $songRepository->findAll();

        //on verifie si on a une requete ajax
        if ($request->get('ajax')) {
            $view = $request->get('ajax') == 1 ? 'playlist/_content.html.twig' : 'playlist/_contentPopup.html.twig';
            dump($request->get('ajax'));
            return new JsonResponse([
                'content' => $this->renderView($view, [
                    'songs' => $songs,
                    'allSongs' => $allSongs,
                    'playlist' => $playlist,
                    'filteredPlaylistSongs' => $filteredPlaylistSongs,
                ])
            ]);
        }
        return $this->render('playlist/index.html.twig', [
            'songs' => $songs, //$songRepository->findAll(),
            'playlist' => $playlist,
            'allSongs' => $allSongs,
            'filteredPlaylistSongs' => $filteredPlaylistSongs,
        ]);
    }

    #[Route('/playlist/add', name: 'app_playlist_add')]
    public function add(Request $request,
                        SluggerInterface $slugger,
                        UploadFileHandler $uploadFileHandler,
                        PlaylistFormHandler $playlistFormHandler ): Response
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
            $directory = $this->getParameter('image_directory');
            $playlistFormHandler->addPlaylist($playlist, $imagePathFile, $uploadFileHandler, $slugger,$directory);

            //On redirige
            return $this->redirectToRoute('app_user', [
                'userId' => $this->getUser()->getId()]);
        }
        return $this->render('playlist/add.html.twig', [
            'playlistForm' => $playlistForm->createView()
        ]);
    }

    #[Route('/playlist/edit/{id}', name: 'app_playlist_edit')]
    public function edit(Playlist $playlist,
                         Request $request,
                         SluggerInterface $slugger,
                         UploadFileHandler $uploadFileHandler,
                         PlaylistFormHandler $playlistFormHandler): Response
    {
        //On crée le formulaire
        $playlistForm = $this->createForm(PlaylistFormType::class, $playlist);
        // On traite la requete du formulaire
        $playlistForm->handleRequest($request);
        // on verifie si le formulaire est soumis et valide
        if ($playlistForm->isSubmitted() && $playlistForm->isValid()) {

            $imagePathFile = $playlistForm->get('imageFileName')->getData();
            $directory = $this->getParameter('image_directory');
            $playlistFormHandler->addPlaylist($playlist, $imagePathFile, $uploadFileHandler, $slugger,$directory);

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
    public function addSongPlaylist(Playlist $playlist, Song $song, SongRepository $songRepository, PlaylistRepository $playlistRepository, EntityManagerInterface $em, Request $request): Response
    {

        // Check if the playlist and song exist
        if (!$playlist || !$song) {
            throw new NotFoundHttpException();
        }
        // Add the song to the playlist
        $playlist->addSongPlaylist($song);
        $em->flush();

        return $this->redirectToRoute('app_playlist', [
            'id' => $playlist->getId(),
        ]);
    }

    #[Route('/playlist/{playlistId}/dsong/{songId}', name: 'delete_song_playlist')]
    #[Entity('playlist', options: ['id' => 'playlistId'])]
    #[Entity('song', options: ['id' => 'songId'])]
    public function deleteSongPlaylist(Playlist $playlist, Song $song, SongRepository $songRepository, PlaylistRepository $playlistRepository, EntityManagerInterface $em, Request $request): Response

    {
        // Check if the playlist and song exist
        if (!$playlist || !$song) {
            throw new NotFoundHttpException();
        }
        // Add the song to the playlist
        $playlist->removeSong($song);
        $em->flush();

        return $this->redirectToRoute('app_playlist', [
            'id' => $playlist->getId(),
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

    #[Route('/playlist/{playlistId}/song/{songId}/player', name: 'app_playlist_player')]
    #[Entity('playlist', options: ['id' => 'playlistId'])]
    #[Entity('song', options: ['id' => 'songId'])]
    public function player(Request $request, Song $song, Playlist $playlist,
                           PlaylistRepository $playlistRepository,
                           CommentRepository $commentRepository,
                           CommentHandler $commentHandler): Response
    {
        $playlistSongs = $playlist->getSongs()->toArray();

        $selectedSongKey = null;
        foreach ($playlistSongs as $key => $value) {
            if ($value->getId() === $song->getId()) {
                $selectedSongKey = $key;
            }
        }

        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentHandler->addComment($comment, $song,$this->getUser());
        }

        return $this->render('player/index.html.twig', [
            'name' => 'app_playlist_player',
            'song' => $song,
            'form' => $form,
            'playlist' => $playlistRepository->find($playlist->getId()),
            'next' => array_key_exists($selectedSongKey + 1, $playlistSongs) ? $playlistSongs[$selectedSongKey + 1] : null,
            'prev' => array_key_exists($selectedSongKey - 1, $playlistSongs) ? $playlistSongs[$selectedSongKey - 1] : null,
            'comments' => $commentRepository->findAll()
        ]);
    }


    #[Route('/song/{songId}/playlist/{playlistId}', name: 'add_song_homepage')]
    #[Entity('playlist', options: ['id' => 'playlistId'])]
    #[Entity('song', options: ['id' => 'songId'])]
    public function addSongHomepage(Playlist $playlist, Song $song, EntityManagerInterface $em): Response
    {
        // Check if the playlist and song exist
        if (!$playlist || !$song) {
            throw new NotFoundHttpException();
        }
        // Add the song to the playlist
        $playlist->addSongPlaylist($song);

        // Persist the changes to the database
        $em->persist($playlist);
        $em->flush();
        return $this->redirectToRoute('app_homepage');

    }

}