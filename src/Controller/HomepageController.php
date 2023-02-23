<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Song;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\PlaylistRepository;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\SongRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/homepage', name: 'app_homepage')]
    public function index(SongRepository $songRepository, PlaylistRepository $playlistRepository, Request $request): Response
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
                'songs' => $songs
            ]);

        }

        return $this->render('homepage/index.html.twig', [
            'form'=>$form->createView(),
            'songs' => $songRepository->findAll(),
            'playlists' => $playlistRepository->findBy(['user' => $this->getUser()]),
        ]);
    }

    #[Route('/homepage/{id}/player', name: 'app_homepage_player', methods: ['GET', 'POST'], requirements: ['id' =>'\d+'])]
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
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setSong($song);
            $em->persist($comment);
            $em->flush();
        }


        return $this->render('player/index.html.twig', [
            'name'=>'app_homepage_player',
            'isPlaylist'=> false,
            'isSong' => false,
            'song' => $song,
            'form' => $form,
            'next' => array_key_exists($selectedSongKey+1, $allSongs) ? $allSongs[$selectedSongKey+1] : null,
            'prev' => array_key_exists($selectedSongKey-1, $allSongs) ? $allSongs[$selectedSongKey-1] : null,
            'comments' => $commentRepository->findAll()
        ]);

    }

}
