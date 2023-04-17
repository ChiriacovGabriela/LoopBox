<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Entity\Song;
use App\Form\PlaylistFormType;
use App\Entity\Comment;
use App\Form\CommentFormType;

use App\FormHandler\CommentHandler;

use App\Repository\CommentRepository;

use App\Repository\PlaylistRepository;
use App\Repository\SongRepository;
use App\FormHandler\UploadFileHandler;
use App\FormHandler\PlaylistFormHandler;

use App\Service\AjaxService;
use App\Service\PlayerService;
use Doctrine\ORM\EntityManagerInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/playlist')]
class PlaylistController extends AbstractController

{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_playlist', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function index(Request $request, SongRepository $songRepository, Playlist $playlist, AjaxService $ajaxService): Response
    {
        $page = $request->query->getInt('page', 1);
        $filters = $request->get('type');
        list($songs, $filteredPlaylistSongs) = $ajaxService->handleFilterPlaylist($filters, $songRepository, $playlist, $page);
        $allSongs = $songRepository->findAll();
        if ($request->get('ajax')) {
            return $ajaxService->handlePlaylistAjaxRequest($request, $songs, $allSongs, $playlist, $filteredPlaylistSongs);
        }
        return $this->render('playlist/index.html.twig', [
            'songs' => $songs,
            'playlist' => $playlist,
            'allSongs' => $allSongs,
            'filteredPlaylistSongs' => $filteredPlaylistSongs,
        ]);
    }

    #[Route('/add', name: 'app_playlist_add')]
    public function add(Request             $request,
                        SluggerInterface    $slugger,
                        UploadFileHandler   $uploadFileHandler,
                        PlaylistFormHandler $playlistFormHandler): Response
    {
        $playlist = new Playlist();
        $playlistForm = $this->createForm(PlaylistFormType::class, $playlist);
        $playlist->setUser($this->getUser());

        $playlistForm->handleRequest($request);
        if ($playlistForm->isSubmitted() && $playlistForm->isValid()) {
            $imagePathFile = $playlistForm->get('imageFileName')->getData();
            $directory = $this->getParameter('image_directory');
            $playlistFormHandler->addPlaylist($playlist, $imagePathFile, $uploadFileHandler, $slugger, $directory);

            return $this->redirectToRoute('app_user', [
                'userId' => $this->getUser()->getId()]);
        }
        return $this->render('playlist/add.html.twig', [
            'playlistForm' => $playlistForm->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'app_playlist_edit')]
    public function edit(Playlist            $playlist,
                         Request             $request,
                         SluggerInterface    $slugger,
                         UploadFileHandler   $uploadFileHandler,
                         PlaylistFormHandler $playlistFormHandler): Response
    {
        $playlistForm = $this->createForm(PlaylistFormType::class, $playlist);
        $playlistForm->handleRequest($request);

        if ($playlistForm->isSubmitted() && $playlistForm->isValid()) {
            $imagePathFile = $playlistForm->get('imageFileName')->getData();
            $directory = $this->getParameter('image_directory');
            $playlistFormHandler->addPlaylist($playlist, $imagePathFile, $uploadFileHandler, $slugger, $directory);

            return $this->redirectToRoute('app_playlist', ['id' => $playlist->getId()]);

        }
        return $this->render('playlist/edit.html.twig', [
            'playlistForm' => $playlistForm->createView(),
            'playlist' => $playlist
        ]);
    }

    #[Route('/{playlistId}/song/{songId}', name: 'add_song_playlist')]
    #[Entity('playlist', options: ['id' => 'playlistId'])]
    #[Entity('song', options: ['id' => 'songId'])]
    public function addSongPlaylist(Playlist $playlist, Song $song, EntityManagerInterface $em): Response
    {
        $playlist->addSongPlaylist($song);
        $em->flush();

        return $this->redirectToRoute('app_playlist', [
            'id' => $playlist->getId(),
        ]);
    }

    #[Route('/{playlistId}/dsong/{songId}', name: 'delete_song_playlist')]
    #[Entity('playlist', options: ['id' => 'playlistId'])]
    #[Entity('song', options: ['id' => 'songId'])]
    public function deleteSongPlaylist(Playlist $playlist, Song $song, EntityManagerInterface $em): Response

    {
        $playlist->removeSong($song);
        $em->flush();

        return $this->redirectToRoute('app_playlist', [
            'id' => $playlist->getId(),
        ]);
    }

    #[Route('/{id}', name: 'app_playlist_delete', methods: ['POST'])]
    public function delete(Request $request, Playlist $playlist, PlaylistRepository $playlistRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $playlist->getId(), $request->request->get('_token'))) {
            $playlistRepository->remove($playlist, true);
        }
        return $this->redirectToRoute('app_user', [
            'userId' => $this->getUser()->getId()]);
    }

    #[Route('/{playlistId}/song/{songId}/player', name: 'app_playlist_player')]
    #[Entity('playlist', options: ['id' => 'playlistId'])]
    #[Entity('song', options: ['id' => 'songId'])]
    public function player(Request            $request, Song $song, Playlist $playlist,
                           PlaylistRepository $playlistRepository,
                           CommentRepository  $commentRepository,
                           CommentHandler     $commentHandler, PlayerService $playerService): Response
    {
        $playlistSongs = $playerService->getSongsArray($playlist);

        $selectedSongKey = $playerService->getKeySongs($playlistSongs, $song);

        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $commentHandler->addComment($comment, $song, $this->getUser());
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
        $playlist->addSongPlaylist($song);

        $em->persist($playlist);
        $em->flush();
        return $this->redirectToRoute('app_homepage');

    }

}