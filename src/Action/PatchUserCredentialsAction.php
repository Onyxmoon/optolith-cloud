<?php

namespace App\Action;

use App\Controller\ConfirmationEmailController;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class PatchUserCredentialsAction extends AbstractController
{
    private UserPasswordEncoderInterface $passwordEncoder;
    private AuthorizationCheckerInterface $authorizationChecker;
    private ConfirmationEmailController $confirmationEmailController;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, AuthorizationCheckerInterface $authorizationChecker, ConfirmationEmailController $confirmationEmailController)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->authorizationChecker = $authorizationChecker;
        $this->confirmationEmailController = $confirmationEmailController;
    }

    public function __invoke(Request $request): User
    {
        //Entity manager
        $entityManager = $this->getDoctrine()->getManager();

        //Get designated user
        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->find($request->attributes->get("id"));

        //Get request body
        $requestContent = \json_decode($request->getContent(), true);

        //Empty?
        if ($requestContent === null) {
            throw new BadRequestHttpException("JSON Body missing or malformed");
        }

        //Get current user password
        $currentPassword = $requestContent["currentPassword"];

        //Validate current user password
        $checkPass = $this->passwordEncoder->isPasswordValid($user, $currentPassword);


        if(($checkPass === true && $this->authorizationChecker->isGranted("ROLE_USER")) || $this->authorizationChecker->isGranted("ROLE_ADMIN")) {
            //Patch password
            if (array_key_exists("newPassword", $requestContent)) {
                $new_pwd_encoded = $this->passwordEncoder->encodePassword($user, $requestContent["newPassword"]);
                $user->setPassword($new_pwd_encoded);

                //Persist changes
                $entityManager->persist($user);
                //Flush changes
                $entityManager->flush();
            }

            //Patch email
            if (array_key_exists("newEmail", $requestContent)) {
                //Check if mail already in use
                $existingUserWithDesignatedMail = $entityManager->getRepository(User::class)->findOneBy(['email' => $requestContent["newEmail"]]);

                if ($existingUserWithDesignatedMail != null) {
                    //Mail is already in use by foreign user
                    if ($existingUserWithDesignatedMail != $user) {
                        throw new BadRequestHttpException("The new mail address is already in use");
                    } else {
                        //New mail address is users current confirmed mail address
                        //Assume that user want to cancel a change because he sets his current address as new
                        $user->setNewEmail(null);
                        $user->setConfirmationSecret("");
                        $user->setConfirmationType(null);
                        $user->setConfirmedEmail(true);
                        $this->getDoctrine()->getManager()->persist($user);
                        $this->getDoctrine()->getManager()->flush();
                    }
                } else {
                    //Mail is available
                    $user->setNewEmail($requestContent["newEmail"]);
                    $user->setConfirmationSecret($user->gen_uuid());
                    $user->setConfirmationType("email");
                    $user->setConfirmedEmail(false);
                    //Persist changes
                    $entityManager->persist($user);
                    //Flush changes
                    $entityManager->flush();

                    //Send mail
                    $this->confirmationEmailController->sendEmailConfirmEmail($user);
                }

            }

            return $user;

        } else {
            throw new HttpException(401, 'Access denied.');
        }
    }
}