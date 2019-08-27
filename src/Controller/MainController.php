<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends Controller
{
    /**
     * @Route("/", name="status_redirect")
     */
    public function main(Request $request)
    {
        return $this->redirect("https://status.optolith.app", 301);
    }
}