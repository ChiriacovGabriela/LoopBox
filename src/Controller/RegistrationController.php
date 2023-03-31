<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request,
                             UserPasswordHasherInterface $userPasswordHasher,
                             EntityManagerInterface $entityManager,
                             MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            $email = (new Email())
                ->from('gabrielachiriacov@gmail.com')
                ->to($user->getEmail())
                ->subject('Time for Symfony Mailer!')
                ->text('Sending emails is fun again!');

            $mailer->send($email);

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    #[Route('/user/edit/{id}', name: 'app_user_edit')]
    public function edit (User $user, Request $request, EntityManagerInterface $em ):Response
    {
        //On crée le formulaire
        $registrationForm = $this->createForm(RegistrationFormType::class, $user);
        // On traite la requete du formulaire
        $registrationForm->handleRequest($request);
        // on verifie si le formulaire est soumis et valide
        if($registrationForm->isSubmitted() && $registrationForm->isValid()){
            //On stock
            $em-> persist($user);
            $em->flush();


            //On redirige
            return $this->redirectToRoute('app_user');

        }
        return $this->render('registration/edit.html.twig', [
            'registrationForm' => $registrationForm->createView(),
            'user' => $user
        ]);

    }
}
