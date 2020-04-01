<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;
use App\Repository\UserRepository;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(UserRepository $userRepo)
    {
        $birthdays = $userRepo->birthdays(date('m-d'));

        return $this->render('home/index.html.twig', [
            'birthdays' => $birthdays
        ]);
    }
}
