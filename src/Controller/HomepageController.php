<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Song;
use App\Repository\AlbumRepository;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\PlaylistRepository;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\SongRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomepageController extends AbstractController
{
    #[Route('/homepage', name: 'app_homepage')]
    public function index(SongRepository $songRepository, PlaylistRepository $playlistRepository, Request $request, AlbumRepository $albumRepository): Response
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
        //dd($filters);
        if ($filters != null) {
            //on recupere les bonnes chansons en fonction des filtres
            $songs = $songRepository->findSongsByType($filters);
        } else {
            $songs = $songRepository->findAll();
        }
        //dd($filters);

        //on verifie si on a une requete ajax
        if ($request->get('ajax')) {
            return new JsonResponse([
                'content' => $this->renderView('homepage/_content.html.twig', [
                    'form' => $form->createView(),
                    'songs' => $songs,
                    'playlists' => $playlistRepository->findBy(['user' => $this->getUser()]),

                ])
            ]);
        }

        return $this->render('homepage/index.html.twig', [
            'form' => $form->createView(),
            'songs' => $songs,
            'playlists' => $playlistRepository->findBy(['user' => $this->getUser()]),
            'albums' => $albumRepository->findAll(),
        ]);
    }

    #[Route('/homepage/{id}/player', name: 'app_homepage_player', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function player(Request $request, Song $song, SongRepository $songRepository, CommentRepository $commentRepository, EntityManagerInterface $em): Response
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
        //dd($form);
        if ($form->isSubmitted() && $form->isValid()){
            $comment->setUser($this->getUser());
            $comment->setSong($song);
            $em->persist($comment);
            $em->flush();
        }


        return $this->render('player/index.html.twig', [
            'name'=>'app_homepage_player',
            'isPlaylist'=> false,
            'isSong' => false,
            'isAlbum' => false,
            'isFavoris' => false,
            'song' => $song,
            'form' => $form,
            'next' => array_key_exists($selectedSongKey + 1, $allSongs) ? $allSongs[$selectedSongKey + 1] : null,
            'prev' => array_key_exists($selectedSongKey - 1, $allSongs) ? $allSongs[$selectedSongKey - 1] : null,
            'comments' => $commentRepository->findAll()
        ]);

    }

}
