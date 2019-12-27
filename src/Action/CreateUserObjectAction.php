<?php


namespace App\Action;

use App\Controller\ConfirmationEmailController;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


final class CreateUserObjectAction
{
    private $confirmationEmailController;

    public function __construct(ConfirmationEmailController $confirmationEmailController)
    {
        $this->confirmationEmailController = $confirmationEmailController;
    }

    public function __invoke(User $data): User
    {
        $this->confirmationEmailController->sendAccountConfirmEmail($data);

        return $data;
    }

}