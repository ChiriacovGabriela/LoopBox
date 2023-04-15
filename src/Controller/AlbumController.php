<?php

namespace App\Controller;

use App\Entity\Album;
use App\Form\AlbumType;
use App\FormHandler\UploadFileHandler;
use App\Repository\AlbumRepository;
use App\Repository\SongRepository;
use App\Service\AlbumManager;
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
    public function new( Request $request,UploadFileHandler $uploadFileHandler,SluggerInterface $slugger, EntityManagerInterface $em,AlbumManager $albumManager): Response
    {
        $album = new Album();
        $user=$this->getUser();
        $album->setUser($user);
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);
        $directoryImage = $this->getParameter('image_directory');
        $directorySong = $this->getParameter('song_directory');
        if($form->isSubmitted() && $form->isValid())
        {
            $albumManager->processForm($album, $uploadFileHandler,$slugger,$form,$directoryImage, $directorySong,$user);
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
    public function edit(Request $request, Album $album, AlbumRepository $albumRepository,SluggerInterface $slugger,UploadFileHandler $uploadFileHandler,AlbumManager $albumManager): Response
    {
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);
        $user=$this->getUser();
        $directoryImage = $this->getParameter('image_directory');
        $directorySong = $this->getParameter('song_directory');
        if ($form->isSubmitted() && $form->isValid()) {
            $albumManager->processForm($album, $uploadFileHandler,$slugger,$form,$directoryImage, $directorySong,$user);
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
