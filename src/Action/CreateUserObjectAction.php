<?php


namespace App\Action;

use App\Controller\ConfirmationEmailController;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;


final class CreateUserObjectAction extends AbstractController
{
    private ConfirmationEmailController $confirmationEmailController;
    private ValidatorInterface $validator;

    public function __construct(ConfirmationEmailController $confirmationEmailController, ValidatorInterface $validator)
    {
        $this->confirmationEmailController = $confirmationEmailController;
        $this->validator = $validator;
    }

    public function __invoke(User $data): User
    {
        if (count($this->validator->validate($data, null, ['user:create'])) == 0) {
            //email address already in use?
            if ($this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $data->getEmail()]) != null) {
                throw new BadRequestHttpException("There is already an user with this e-mail address");
            }

            //Validation okay and email not in use -> send confirmation mail
            $this->confirmationEmailController->sendAccountConfirmEmail($data);
        }
        return $data;
    }

}