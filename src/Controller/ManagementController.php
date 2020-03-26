<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ManagementController extends AbstractController
{
    /**
     * @Route("/management/staff", name="staff_management")
     */
    public function staffManagement(UserRepository $userRepository)
    {
        $staff = $userRepository->getByRole('"ROLE_USER", "ROLE_MODO"');
        $users = $userRepository->getByRole('"ROLE_USER"');

        return $this->render('management/index.html.twig', [
            'controller_name' => 'HomeController',
            'staff' => $staff,
            'users' => $users
        ]);

    }
}
