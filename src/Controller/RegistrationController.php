<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationController extends AbstractController
{
    private $verifyEmailHelper;
    private $mailer;
    
    public function __construct(VerifyEmailHelperInterface $helper, MailerInterface $mailer)
    {
        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailer;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new Utilisateur();
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
            
            $signatureComponents = $this->verifyEmailHelper->generateSignature(
                'app_registration_confirmation_route',
                $user->getId(),
                $user->getEmail()
            );
        
        
            $email = new TemplatedEmail();
            $email->from('FluxSell@Corp.com');
            $email->to($user->getEmail());
            $email->htmlTemplate('emails/email.html.twig');
            $email->context(['signedUrl' => $signatureComponents->getSignedUrl()]);
        
            $this->mailer->send($email);
    
        // generate and return a response for the browser
            

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }



    #[Route('/api/register', name: 'app_api_register')]
    public function apiRegister(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        
        $user = $serializer->deserialize($request->getContent(), Utilisateur::class, 'json');

        $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                )
        );
        
        //$form = $this->createForm(RegistrationFormType::class, $user);
        
        //$form->handleRequest($request);

        //if ($form->isSubmitted() && $form->isValid()) {
            
            // encode the plain password
           
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            
            $signatureComponents = $this->verifyEmailHelper->generateSignature(
                'app_registration_confirmation_route',
                $user->getId(),
                $user->getEmail()
            );
        
        
            $email = new TemplatedEmail();
            $email->from('FluxSell@Corp.com');
            $email->to($user->getEmail());
            $email->htmlTemplate('emails/email.html.twig');
            $email->context(['signedUrl' => $signatureComponents->getSignedUrl()]);
        
            $this->mailer->send($email);
    
        // generate and return a response for the browser
            

            return $this->redirectToRoute('app_login');
        //}

    }



    #[Route('/verify', name: 'app_registration_confirmation_route')]
    public function verifyUserEmail(Request $request,EntityManagerInterface $entityManager): Response
    {
        //Je recupère le token de la requête afin de retrouver l'utilisateur 
        $token = $request->get("token");
        
        //Il faut que je retrouve l'utilisateur en base ici même
        $payload = json_decode(base64_decode($token,true));
        
        $user = $entityManager->find(Utilisateur::class,(int)$payload["0"]);
        
        // Avant Symfony $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // Symfony $user = $this->getUser();
        
        // Do not get the User's Id or Email Address from the Request object
        try {
            $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('verify_email_error', $e->getReason());

            return $this->redirectToRoute('app_register');
        }

        // Mark your user as verified. e.g. switch a User::verified property to true
        $user->setIsVerified(true);
        $entityManager->flush();
        $this->addFlash('success', 'Your e-mail address has been verified.');

        return $this->redirectToRoute('api_entrypoint');
    }
}
