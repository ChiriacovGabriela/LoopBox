<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\FormHandler\SendMailHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
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
                             SendMailHandler $mailer): Response

    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $users = $entityManager->getRepository(User::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($users as $u) {
                if ($u->getEmail() == $user->getEmail()) {
                    return $this->render('registration/register.html.twig', [
                        'emailAlreadyUsed' => true,
                        'registrationForm' => $form->createView(),
                    ]);
                }
            }

            if($user->getEmail())
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $mailer->sendEmail($user);

            return $this->redirectToRoute('app_login');

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/user/edit/{id}', name: 'app_user_edit')]
    public function edit(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $registrationForm = $this->createForm(RegistrationFormType::class, $user);
        $registrationForm->handleRequest($request);
        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_user',[
                'userId'=>$user->getId()
            ]);

        }
        return $this->render('registration/edit.html.twig', [
            'registrationForm' => $registrationForm->createView(),
            'user' => $user
        ]);

    }
}
