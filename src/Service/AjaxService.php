<?php

namespace App\Service;

use App\Entity\Playlist;
use App\Repository\SongRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AjaxService
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function handleHomepageAjaxRequest(Request $request, array $songs, array $playlists, $formView): JsonResponse
    {
        return new JsonResponse([
            'content' => $this->twig->render('homepage/_content.html.twig', [
                'form' => $formView,
                'songs' => $songs,
                'playlists' => $playlists,
            ])
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function handlePlaylistAjaxRequest(Request $request, array $songs, array $allSongs, Playlist $playlist, array $filteredPlaylistSongs): JsonResponse
    {
        // Determine which view to use based on the value of the 'ajax' parameter
        if ($request->get('ajax') == 1) {
            $view = 'playlist/_content.html.twig';
        } else {
            $view = 'playlist/_contentPopup.html.twig';
        }

        return new JsonResponse([
            'content' => $this->twig->render($view, [
                'songs' => $songs,
                'allSongs' => $allSongs,
                'playlist' => $playlist,
                'filteredPlaylistSongs' => $filteredPlaylistSongs,
            ])
        ]);
    }

    public function handleFilterPlaylist($filters, SongRepository $songRepository, Playlist $playlist, int $page): array
    {
        if ($filters != null) {
            $songs = $songRepository->findSongsByType($filters);
            $filteredPlaylistSongs = $songRepository->findSongsByPlaylistAndType($playlist, $filters, $page, 5);
        } else {
            $songs = $songRepository->findAll();
            $filteredPlaylistSongs = $songRepository->findSongsByPlaylistPaginated($playlist, $page, 5);
        }
        return [$songs, $filteredPlaylistSongs];
    }

    public function handleFilterHomepage($filters, SongRepository $songRepository): array
    {
        if ($filters != null) {
            $songs = $songRepository->findSongsByType($filters);
        } else {
            $songs = $songRepository->findAll();
        }
        return $songs;
    }
}