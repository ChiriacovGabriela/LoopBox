<?php

namespace App\Controller;

use App\Entity\Song;
use App\Repository\AlbumRepository;
use App\Repository\PlaylistRepository;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\SongRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class HomepageController extends AbstractController
{
    #[Route('/homepage', name: 'app_homepage')]
    public function index(SongRepository $songRepository, PlaylistRepository $playlistRepository, Request $request, AlbumRepository $albumRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class,$searchData);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $searchData->page=$request->query->getInt('page',1);
            $songs = $songRepository->findBySearch($searchData);

            return $this->render('homepage/index.html.twig',[
                'playlists' => $playlistRepository->findBy(['user' => $this->getUser()]),
                'form' => $form,
                'songs' => $songs,
                'albums' => $albumRepository->findAll(),

            ]);

        }
        return $this->render('homepage/index.html.twig', [
            'form'=>$form->createView(),
            'songs' => $songRepository->findAll(),
            'playlists' => $playlistRepository->findBy(['user' => $this->getUser()]),
            'albums' => $albumRepository->findAll(),
        ]);


    }

    #[Route('/homepage/{id}/player', name: 'app_homepage_player', methods: ['GET'], requirements: ['id' =>'\d+'])]
    public function player(Song $song, SongRepository $songRepository): Response
    {
        $allSongs = $songRepository->findAll();

        $selectedSongKey = null;
        foreach ($allSongs as $key => $value) {
            if ($value->getId() === $song->getId()) {
                $selectedSongKey = $key;
            }
        }

        return $this->render('homepage/player.html.twig', [
            'song' => $song,
            'next' => array_key_exists($selectedSongKey+1, $allSongs) ? $allSongs[$selectedSongKey+1] : null,
            'prev' => array_key_exists($selectedSongKey-1, $allSongs) ? $allSongs[$selectedSongKey-1] : null,
        ]);


    }




}
