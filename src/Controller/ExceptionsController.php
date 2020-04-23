<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ExceptionsController extends AbstractController
{
    /**
     * @Route("/exceptions", name="exceptions")
     */
    public function index()
    {
        return $this->render('exceptions/404.html.twig', [
            'reason' => 'content',
        ]);
    }
}
