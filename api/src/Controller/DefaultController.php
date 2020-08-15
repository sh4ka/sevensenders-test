<?php


namespace App\Controller;

use App\ValueObjects\Responses;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="healthcheck")
     */
    public function healthcheck()
    {
        return $this->json(Responses::getOkResponse(), Response::HTTP_OK);
    }
}