<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfirmationEmailController extends AbstractController {

    private $mailer;
    private $translator;
    private $router;
    private $fromAddress;

    public function __construct(MailerInterface $mailer, TranslatorInterface $translator, UrlGeneratorInterface $router)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->router = $router;
        $this->fromAddress = new Address('account@cloud.optolith.app', 'Optolith Cloud | Account');
    }

    /**
     * @Route("confirm/account/{confirmationSecret}", name="confirm_account")
     * @param string $confirmationSecret
     * @return Response
     * @throws HttpException|NotFoundHttpException
     */
    public function confirmAccount(string $confirmationSecret) {
        /** @var User $user get designated user for secret*/
        $user = $this->getDoctrine()->getManager()
            ->getRepository(User::class)->findOneBy( ['confirmationSecret' => $confirmationSecret] );


        if ($user) {
            //Check confirmationType
            if ($user->getConfirmationType() != "account") {
                throw new HttpException(500, "The secret is not for an account confirmation!");
            }

            $user->setConfirmationSecret("");
            $user->setConfirmationType(null);
            $user->setIsActive(true);
            $user->setConfirmedEmail(true);

            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
        } else {
            throw new NotFoundHttpException();
        }

        return new Response("Account verified!", Response::HTTP_ACCEPTED);
    }

    /**
     * @Route("confirm/email/{confirmationSecret}", name="confirm_email")
     * @param string $confirmationSecret
     * @return Response
     */
    public function confirmEmail(string $confirmationSecret) {
        /** @var User $user get designated user for secret*/
        $user = $this->getDoctrine()->getManager()
            ->getRepository(User::class)->findOneBy( ['confirmationSecret' => $confirmationSecret] );


        if ($user) {
            //Check confirmationType
            if ($user->getConfirmationType() != "email") {
                throw new HttpException(500, "The secret is not for an mail confirmation!");
            }

            $user->setEmail($user->getNewEmail());
            $user->setNewEmail(null);
            $user->setConfirmationSecret("");
            $user->setConfirmationType(null);
            $user->setConfirmedEmail(true);

            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
        } else {
            throw new NotFoundHttpException();
        }

        return new Response("Mail verified!", Response::HTTP_ACCEPTED);
    }

    public function sendAccountConfirmEmail(User $user) {
        $greetings = $this->translator->trans("Hello", [], "confirmationEmail", $user->getLocale()) . " " . $user->getDisplayName() . ",";
        $text = $this->translator->trans("Welcome to the Optolith Cloud. With this e-mail you will get the ability to verify your account and confirm your e-mail address.", null, "confirmationEmail", $user->getLocale());

        $accountConfirmMail = (new TemplatedEmail())
            ->from($this->fromAddress)
            ->to($user->getEmail())
            ->subject($this->translator->trans("Confirm your account", [], "confirmationEmail", $user->getLocale()))

            //Set template for rendering mail
            ->htmlTemplate("email/confirmationEmail/confirm.email.html.twig")

            //Pass variables for twig email template
            ->context([
                "subject" => $this->translator->trans("Confirm your account", [], "confirmationEmail", $user->getLocale()),
                "greetings" => $greetings,
                "text" => $text,
                "confirmationLink" => $this->router->generate('confirm_account', ['confirmationSecret' => $user->getConfirmationSecret()], UrlGeneratorInterface::ABSOLUTE_URL),
                "confirmationButtonText" => $this->translator->trans("Verify account", [], "confirmationEmail", $user->getLocale())
            ]);

        try {
            $this->mailer->send($accountConfirmMail);
        } catch (TransportExceptionInterface $e) {
            throw new HttpException(500, "Error while sending account confirmation mail");
        }
    }

    public function sendEmailConfirmEmail(User $user) {
        $greetings = $this->translator->trans("Hello", [], "confirmationEmail", $user->getLocale()) . " " . $user->getDisplayName() . ",";
        $text = $this->translator->trans("You are receiving this message because you have changed your e-mail address. Please verify your new address, then you can use it for future registrations.", [], "confirmationEmail", $user->getLocale());

        $emailConfirmMail = (new TemplatedEmail())
            ->from($this->fromAddress)
            ->to($user->getNewEmail())
            ->subject($this->translator->trans("Confirm your new mail address", [], "confirmationEmail", $user->getLocale()))

            //Set template for rendering mail
            ->htmlTemplate("email/confirmationEmail/confirm.email.html.twig")

            //Pass variables for twig email template
            ->context([
                "subject" => $this->translator->trans("Confirm your new mail address", [], "confirmationEmail", $user->getLocale()),
                "greetings" => $greetings,
                "text" => $text,
                "confirmationLink" => $this->router->generate('confirm_email', ['confirmationSecret' => $user->getConfirmationSecret()], UrlGeneratorInterface::ABSOLUTE_URL),
                "confirmationButtonText" => $this->translator->trans("Verify e-mail address", [], "confirmationEmail", $user->getLocale())
            ]);

        try {
            $this->mailer->send($emailConfirmMail);
        } catch (TransportExceptionInterface $e) {
            throw new HttpException(500, "Error while sending account confirmation mail : " .$e->getMessage() );
        }
    }

}