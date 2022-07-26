<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Utilisateur;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class UtilisateurProcessingSubscriber implements EventSubscriberInterface
{
    private $userPasswordHasher;
    private $mailer;
    private $verifyEmailHelper;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, MailerInterface $mailer, VerifyEmailHelperInterface $helper) {
        $this->verifyEmailHelper = $helper;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->mailer = $mailer;
    }

    public function userPasswordHashing(RequestEvent $event): void
    {
        $utilisateur = $event->getRequest()->get('data');
        if($utilisateur instanceof Utilisateur){
            // encode the plain password
            $utilisateur->setPassword(
                $this->userPasswordHasher->hashPassword(
                        $utilisateur,
                        $utilisateur->getPassword()
                    )
                );
        }
    }

    public function envoieDeMail(ViewEvent $event)
    {
        $utilisateur = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$utilisateur instanceof Utilisateur || Request::METHOD_POST !== $method) {
            return;
        }
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'app_registration_confirmation_route',
            $utilisateur->getId(),
            $utilisateur->getEmail()
        );
    
    
        $email = new TemplatedEmail();
        $email->from('eengelbertdesire@gmail.com');
        $email->to($utilisateur->getEmail());
        $email->htmlTemplate('emails/email.html.twig');
        $email->context(['signedUrl' => $signatureComponents->getSignedUrl()]);
    
        $this->mailer->send($email);

    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['userPasswordHashing', EventPriorities::POST_DESERIALIZE],
            KernelEvents::VIEW => ['envoieDeMail', EventPriorities::POST_WRITE],
        ];
    }
}
