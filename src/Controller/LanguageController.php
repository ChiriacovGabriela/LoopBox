<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;




class LanguageController extends AbstractController
{
    #[Route('/change-locale/{locale}', name: 'app_change_locale')]
    public function changeLocale($locale, Request $request)
    {
        //On stocke la langue demandée dans la session
        $request->getSession()->set('_locale',$locale);
        //On revient sur la page précédente
        //permet à l'utilisateur de changer la langue et de continuer à utiliser l'application à partir de la même page qu'il était
        return $this->redirect($request->headers->get('referer'));


    }
}