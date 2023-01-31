<?php

namespace App\Controller;

use App\Entity\Song;
use App\Form\SongType;
use App\FormHandler\UploadFileHandler;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/song')]
class SongController extends AbstractController
{
    #[Route('/', name: 'app_song_index', methods: ['GET'])]
    public function index(SongRepository $songRepository): Response
    {
        $songs = $songRepository->findBy(['user'=>$this->getUser()]);

        return $this->render('song/index.html.twig', [
            'songs' => $songs
        ]);
    }


    #[Route('/new', name: 'app_song_new', methods: ['GET', 'POST'])]
    public function new(Request $request,EntityManagerInterface $em ,SluggerInterface $slugger, UploadFileHandler $uploadFileHandler): Response
    {
        $song = new Song();
        $song ->setUser($this->getUser());
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $songFile*/
            $songFile = $form->get('audioFileName')->getData();
            $imageFile = $form ->get('pictureFileName')->getData();
            if ($songFile){
                $originalFilename = pathinfo($songFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$songFile->guessExtension();

                try {
                    $songFile->move(
                        $this->getParameter('song_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $song->setAudioFileName($newFilename);
            }
            if($imageFile){
                $directory = $this->getParameter('image_directory');
                $newFilename = $uploadFileHandler->upload($slugger,$imageFile,$directory);
                $song->setPictureFileName($newFilename);
            }


            $em-> persist($song);
            $em->flush();

            return $this->redirectToRoute('app_song_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('song/new.html.twig', [
            'song' => $song,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_song_show', methods: ['GET'])]
    public function show(Song $song): Response
    {
        return $this->render('song/show.html.twig', [
            'song' => $song,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_song_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Song $song, SongRepository $songRepository,SluggerInterface $slugger,UploadFileHandler $uploadFileHandler): Response
    {
        $song -> setUpdatedAt(new \DateTimeImmutable());
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $songFile = $form->get('audioFileName')->getData();
            $imageFile = $form ->get('pictureFileName')->getData();
            if ($songFile){
                $originalFilename = pathinfo($songFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$songFile->guessExtension();

                try {
                    $songFile->move(
                        $this->getParameter('song_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $song->setAudioFileName($newFilename);
            }
            if($imageFile){
                $directory = $this->getParameter('image_directory');
                $newFilename = $uploadFileHandler->upload($slugger,$imageFile,$directory);
                $song->setPictureFileName($newFilename);
            }


            $songRepository->save($song, true);

            return $this->redirectToRoute('app_song_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('song/edit.html.twig', [
            'song' => $song,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_song_delete', methods: ['POST'])]
    public function delete(Request $request, Song $song, SongRepository $songRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$song->getId(), $request->request->get('_token'))) {
            $songRepository->remove($song, true);
        }

        return $this->redirectToRoute('app_song_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/player', name: 'app_song_player', methods: ['GET'], requirements: ['id' =>'\d+'])]
    public function player(Song $song, SongRepository $songRepository): Response
    {
        $songs = $songRepository->findBy(['user'=>$this->getUser()]);

        $selectedSongKey = null;
        foreach ($songs as $key => $value) {
            if ($value->getId() === $song->getId()) {
                $selectedSongKey = $key;
            }
        }

        return $this->render('song/player.html.twig', [
            'song' => $song,
            'next' => array_key_exists($selectedSongKey+1, $songs) ? $songs[$selectedSongKey+1] : null,
            'prev' => array_key_exists($selectedSongKey-1, $songs) ? $songs[$selectedSongKey-1] : null,
        ]);


    }
}