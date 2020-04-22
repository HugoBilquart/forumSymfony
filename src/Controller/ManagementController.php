<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\EntityManagerInterface;

use App\Form\StaffManagementType;

class ManagementController extends AbstractController
{
    /**
     * @Route("/management", name="management")
     */
    public function management()
    {
        if($this->getUser() && $this->isGranted('ROLE_MODO')) {
            return $this->render('management/index.html.twig');
        }   
        else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/management/staff", name="staff_management")
     */
    public function staffManagement(Request $request, UserRepository $userRepository, EntityManagerInterface $manager)
    {
        $form = $this->createForm(StaffManagementType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            //Demote user
            if ($form->get('Demote')->isClicked()){
                if($form->get('user')->getData()->getRoles() == ["ROLE_USER","ROLE_MODO","ROLE_ADMIN"]) {
                    $callback = 'ERROR : ADMIN can\'t edit his own hierarchy position';
                }
                else if($form->get('user')->getData()->getRoles() == ["ROLE_USER"]) {
                    $callback = 'ERROR : '.$form->get('user')->getData()->getUsername().' is already a user';
                }
                else {
                    $form->get('user')->getData()->setRoles(['ROLE_USER']);
                    $manager->flush();
                    $callback = $form->get('user')->getData()->getUsername(). ' is demoted';
                }
            }
            //END Demote user
            //Promote user
            else if($form->get('Promote')->isClicked()) {
                if($form->get('user')->getData()->getRoles() == ["ROLE_USER","ROLE_MODO","ROLE_ADMIN"]) {
                    $callback = 'ERROR : ADMIN can\'t edit his own hierarchy position';
                }
                else if($form->get('user')->getData()->getRoles() == ["ROLE_USER","ROLE_MODO"]) {
                    $callback = 'ERROR : '.$form->get('user')->getData()->getUsername().' is already a moderator';
                }
                else {
                    $form->get('user')->getData()->setRoles(['ROLE_USER','ROLE_MODO']);
                    $manager->flush();
                    $callback = $form->get('user')->getData()->getUsername(). ' is promoted as Moderator';
                }
            }
            //END Promote user

            //Get users and moderators
            $staff = $userRepository->getFullUserByRole('"ROLE_USER", "ROLE_MODO"');
            $users = $userRepository->getFullUserByRole('"ROLE_USER"');

            //Return view with processing callback
            return $this->render('management/staffManagement.html.twig', [
                'staff' => $staff,
                'users' => $users,
                'form'  => $form->createView(),
                'results' => $callback
            ]);
        }
        else {
            //Get users and moderators
            $staff = $userRepository->getFullUserByRole('"ROLE_USER", "ROLE_MODO"');
            $users = $userRepository->getFullUserByRole('"ROLE_USER"');

            return $this->render('management/staffManagement.html.twig', [
                'staff' => $staff,
                'users' => $users,
                'form'  => $form->createView()
            ]);
        }
    }
}
