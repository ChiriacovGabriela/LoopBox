<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Song;
use App\Form\CommentFormType;
use App\Form\SongType;

use App\Repository\CommentRepository;
use App\FormHandler\UploadFileHandler;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        $songs = $songRepository->findBy(['user' => $this->getUser()]);

        return $this->render('song/index.html.twig', [
            'songs' => $songs
        ]);
    }


    #[Route('/new', name: 'app_song_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, UploadFileHandler $uploadFileHandler): Response
    {
        $song = new Song();
        $song->setUser($this->getUser());
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $songFile */
            $songFile = $form->get('audioFileName')->getData();
            $imageFile = $form->get('pictureFileName')->getData();
            if ($songFile) {
                $originalFilename = pathinfo($songFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $songFile->guessExtension();

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
            if ($imageFile) {
                $directory = $this->getParameter('image_directory');
                $newFilename = $uploadFileHandler->upload($slugger, $imageFile, $directory);
                $song->setPictureFileName($newFilename);
            }


            $em->persist($song);
            $em->flush();

            return $this->redirectToRoute('app_song_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('song/new.html.twig', [
            'song' => $song,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_song_show', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function show(Request $request, Song $song, CommentRepository $commentRepository, EntityManagerInterface $em): Response
    {
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

        return $this->render('song/show.html.twig', [
            'song' => $song,
            'form' => $form,
            'id' => $song->getId(),
            'comments' => $commentRepository->findAll()
        ]);
    }

    #[Route('/{songId}/comment/{commentId}', name: 'app_comment_delete', methods: ['GET', 'POST'])]
    #[Entity('song', options: ['id' => 'songId'])]
    #[Entity('comment', options: ['id' => 'commentId'])]
    public function deleteComment(Request $request, Song $song, Comment $comment, CommentRepository $commentRepository): Response
    {
        $user = $this->getUser();
        $id = $user->getId();
        if ($id != $comment->getUser()->getId()) {
            throw $this->createAccessDeniedException('You cant delete a comment you didnt post');
        }
        $commentRepository->remove($comment, true);


        return $this->redirectToRoute('app_song_player', ['id' => $song->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/edit', name: 'app_song_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Song $song, SongRepository $songRepository, SluggerInterface $slugger, UploadFileHandler $uploadFileHandler): Response
    {
        $song->setUpdatedAt(new \DateTimeImmutable());
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $songFile = $form->get('audioFileName')->getData();
            $imageFile = $form->get('pictureFileName')->getData();
            if ($songFile) {
                $originalFilename = pathinfo($songFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $songFile->guessExtension();

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
            if ($imageFile) {
                $directory = $this->getParameter('image_directory');
                $newFilename = $uploadFileHandler->upload($slugger, $imageFile, $directory);
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


    #[Route('/delete/{id}', name: 'app_song_delete', requirements: ['id' => '\d+'])]
    public function delete(Request $request, Song $song, SongRepository $songRepository, CommentRepository $commentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $song->getId(), $request->request->get('_token'))) {
            $comments = $commentRepository->findBy(['song' => $song]);
            foreach ($comments as $comment) {
                $commentRepository->remove($comment, true);
            }
            $songRepository->remove($song, true);
        }

        return $this->redirectToRoute('app_song_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/player', name: 'app_song_player', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function player(Song $song, SongRepository $songRepository): Response
    {
        $songs = $songRepository->findBy(['user' => $this->getUser()]);

        $selectedSongKey = null;
        foreach ($songs as $key => $value) {
            if ($value->getId() === $song->getId()) {
                $selectedSongKey = $key;
            }
        }

        return $this->render('song/player.html.twig', [
            'song' => $song,
            'next' => array_key_exists($selectedSongKey + 1, $songs) ? $songs[$selectedSongKey + 1] : null,
            'prev' => array_key_exists($selectedSongKey - 1, $songs) ? $songs[$selectedSongKey - 1] : null,
        ]);


    }

    #[Route('/favoris/add/{id}', name: 'app_song_favoris_add')]
    public function addFavoris(Song $song, EntityManagerInterface $em, int $id)
    {
        if (!$song) {
            throw  new NotFoundHttpException('Musique introuvable');
        }
        $song->addFavori($this->getUser());
        $em->persist($song);
        $em->flush();
        return $this->redirectToRoute('app_song_player', ['id' => $id], Response::HTTP_SEE_OTHER);
    }

    #[Route('/favoris/remove/{id}', name: 'app_song_favoris_remove')]
    public function removeFavoris(Song $song, EntityManagerInterface $em, int $id)
    {
        if (!$song) {
            throw  new NotFoundHttpException('Musique introuvable');
        }
        $song->removeFavori($this->getUser());
        $em->persist($song);
        $em->flush();
        return $this->redirectToRoute('app_song_player', ['id' => $id], Response::HTTP_SEE_OTHER);
    }
}