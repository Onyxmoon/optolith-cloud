<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginFormSecurityController extends AbstractController
{
    /**
     * @Route("security/loginForm", name="security_loginForm")
     */
    public function login(): Response
    {
        return $this->render('security/login.html.twig');
    }
}
