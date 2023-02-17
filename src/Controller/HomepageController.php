<?php

namespace App\Controller;

use App\Entity\Song;
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
    public function index(SongRepository $songRepository, Request $request): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class,$searchData);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $searchData->page=$request->query->getInt('page',1);
            $allSongs = $songRepository->findAll();
            $songs = $songRepository->findBySearch($searchData);

            return $this->render('homepage/index.html.twig',[
                'form' => $form,
                'songs' => $songs
            ]);
        }


        return $this->render('homepage/index.html.twig', [
            'form'=>$form->createView(),
            'songs' => $songRepository->findAll(),
        ]);
    }
}
