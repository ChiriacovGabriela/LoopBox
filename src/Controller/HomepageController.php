<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Song;
use App\FormHandler\CommentHandler;
use App\Repository\AlbumRepository;
use App\Form\CommentFormType;
use App\Form\SearchType;
use App\Repository\CommentRepository;
use App\Repository\PlaylistRepository;
use App\Repository\SongRepository;

use App\Model\SearchData;
use App\Service\AjaxService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/homepage', name: 'app_homepage')]
    public function index(SongRepository $songRepository, PlaylistRepository $playlistRepository, Request $request, AlbumRepository $albumRepository, AjaxService $ajaxService): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $songs = $songRepository->findBySearch($searchData);
            return $this->render('homepage/index.html.twig', [
                'playlists' => $playlistRepository->findBy(['user' => $this->getUser()]),
                'form' => $form,
                'songs' => $songs,
                'albums' => $albumRepository->findAll(),

            ]);

        }
        //on recupere les filtres
        $filters = $request->get('type');
        $songs = $ajaxService->handleFilterHomepage($filters, $songRepository);

        //on verifie si on a une requete ajax
        if ($request->get('ajax')) {
            return $ajaxService->handleHomepageAjaxRequest($request, $songs, $playlistRepository->findBy(['user' => $this->getUser()]), $form->createView());
        }

        return $this->render('homepage/index.html.twig', [
            'form' => $form->createView(),
            'songs' => $songs,
            'playlists' => $playlistRepository->findBy(['user' => $this->getUser()]),
            'albums' => $albumRepository->findAll(),
        ]);
    }

    #[Route('/homepage/{id}/player', name: 'app_homepage_player', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function player(Request $request, Song $song, SongRepository $songRepository,
                           CommentRepository $commentRepository,
                           CommentHandler $commentHandler): Response
    {
        $allSongs = $songRepository->findAll();

        $selectedSongKey = null;
        foreach ($allSongs as $key => $value) {
            if ($value->getId() === $song->getId()) {
                $selectedSongKey = $key;
            }
        }

        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $commentHandler->addComment($comment, $song, $this->getUser());
        }

        return $this->render('player/index.html.twig', [
            'name'=>'app_homepage_player',
            'isHomepage'=> true,
            'song' => $song,
            'form' => $form,
            'comments' => $commentRepository->findAll(),
            'next' => array_key_exists($selectedSongKey + 1, $allSongs) ? $allSongs[$selectedSongKey + 1] : null,
            'prev' => array_key_exists($selectedSongKey - 1, $allSongs) ? $allSongs[$selectedSongKey - 1] : null
        ]);

    }

}
