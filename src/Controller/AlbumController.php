<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Song;
use App\Form\AlbumType;
use App\FormHandler\UploadFileHandler;
use App\Repository\AlbumRepository;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


#[Route('/album')]
class AlbumController extends AbstractController
{
    #[Route('/', name: 'app_album_index', methods: ['GET'])]
    public function index(AlbumRepository $albumRepository): Response
    {
        return $this->render('album/index.html.twig', [
            'albums' => $albumRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_album_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AlbumRepository $albumRepository,EntityManagerInterface $em,SluggerInterface $slugger, UploadFileHandler $uploadFileHandler): Response
    {
        $album = new Album();
        $form = $this->createForm(AlbumType::class, $album);
        $album->setUser($this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $songs= $form->get('songs')->getData();
            $artist= $form->get('artist')->getData();
            $type= $form->get('type')->getData();
            $imageFile = $form ->get('pictureFileName')->getData();
            if($imageFile){
                $directory = $this->getParameter('image_directory');
                $newFilename = $uploadFileHandler->upload($slugger,$imageFile,$directory);
            }
            foreach ($songs as $song){
                $songFile= $song;
                $originalFilename = pathinfo($songFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $songFile =  $safeFilename.'-'.uniqid().'.'.$songFile->guessExtension();;
                $song->move(
                    $this->getParameter('song_directory'),$songFile
                );
                $newSong=new Song();
                $newSong->setArtist($artist);
                $newSong->setType($type);
                $newSong->setName($originalFilename);
                $newSong->setAudioFileName($songFile);
                $newSong->setPictureFileName($newFilename);
                $album->setPictureFileName($newFilename);
                $album->addSong($newSong);
            }

            $em->persist($album);
            $em->flush();
            return $this->redirectToRoute('app_album_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('album/new.html.twig', [
            'album' => $album,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_album_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Album $album): Response
    {
        return $this->render('album/show.html.twig', [
            'album' => $album,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_album_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Album $album, AlbumRepository $albumRepository,SluggerInterface $slugger,UploadFileHandler $uploadFileHandler): Response
    {
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $songs= $form->get('songs')->getData();
            $artist= $form->get('artist')->getData();
            $type= $form->get('type')->getData();
            $imageFile = $form ->get('pictureFileName')->getData();
            if($imageFile){
                $directory = $this->getParameter('image_directory');
                $newFilename = $uploadFileHandler->upload($slugger,$imageFile,$directory);
            }
            foreach ($songs as $song){
                $songFile= $song;
                $originalFilename = pathinfo($songFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $songFile =  $safeFilename.'-'.uniqid().'.'.$songFile->guessExtension();;
                $song->move(
                    $this->getParameter('song_directory'),$songFile
                );
                $newSong=new Song();
                $newSong->setArtist($artist);
                $newSong->setType($type);
                $newSong->setName($originalFilename);
                $newSong->setAudioFileName($songFile);
                $newSong->setPictureFileName($newFilename);
                $album->setPictureFileName($newFilename);
                $album->addSong($newSong);
            }


            $albumRepository->save($album, true);

            return $this->redirectToRoute('app_album_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('album/edit.html.twig', [
            'album' => $album,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_album_delete', methods: ['POST'])]
    public function delete(Request $request, Album $album, AlbumRepository $albumRepository, SongRepository $songRepository): Response
    {

        if ($this->isCsrfTokenValid('delete'.$album->getId(), $request->request->get('_token'))) {
            $songs= $album->getSongs();
            foreach ($songs as $song){
                $songRepository->remove($song, true);
            }
            $albumRepository->remove($album, true);


        }

        return $this->redirectToRoute('app_album_index', [], Response::HTTP_SEE_OTHER);
    }


}
