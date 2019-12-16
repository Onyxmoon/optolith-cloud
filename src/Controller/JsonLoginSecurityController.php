<?php

namespace App\Controller;

use ApiPlatform\Core\Api\IriConverterInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JsonLoginSecurityController extends AbstractController
{
    /**
     * @Route("/security/jsonLogin", name="security_jsonLogin")
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse|Response
     */
    public function jsonLogin(IriConverterInterface $iriConverter)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json(['error' => 'Invalid login request - The content type needs to be "application/json"'], 400);
        }

        return new Response(null, 204, [
            'Location' => $iriConverter->getIriFromItem($this->getUser())
        ]);
    }

    /**
     * @Route("security/logout", name="security_logout")
     * @throws Exception
     */
    public function logout() {
        throw new Exception("Shouldn't be reached");
    }
}
