<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Song;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\PlaylistRepository;
use App\Repository\SongRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/homepage', name: 'app_homepage')]
    public function index(SongRepository $songRepository, PlaylistRepository $playlistRepository): Response
    {
        return $this->render('homepage/index.html.twig', [
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

        return $this->render('homepage/player.html.twig', [
            'song' => $song,
            'form' => $form,
            'next' => array_key_exists($selectedSongKey+1, $allSongs) ? $allSongs[$selectedSongKey+1] : null,
            'prev' => array_key_exists($selectedSongKey-1, $allSongs) ? $allSongs[$selectedSongKey-1] : null,
            'comments' => $commentRepository->findAll()
        ]);


    }




}
