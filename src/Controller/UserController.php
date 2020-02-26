<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\User;
use App\Repository\UserRepository;

use App\Form\RegisterType;
use App\Form\EditProfileDetailsType;

class UserController extends AbstractController
{
    /**
     * @Route("/users", name="users")
     */
    public function index(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/user/{id}", name="profile")
     */
    public function profile(User $user)
    {
        return $this->render('user/profile.html.twig', [
            'data' => $user
        ]);
    }

    /**
     * @Route("/editProfile/details/{id}", name="editProfileDetails")
     */
    public function details(User $user, Request $request)
    {
        if($this->getUser() && $this->getUser() == $user) {
            $form = $this->createForm(EditProfileDetailsType::class, $user);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
    
                return $this->redirectToRoute('profile', array(
                    'id' => $user,
                ));
            }

            return $this->render('user/editProfileDetails.html.twig', [
                'form'  =>  $form->createView()
            ]);
        }
        else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

	    $form->handleRequest($request);
	    
	    if($form->isSubmitted() && $form->isValid()){

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->createAvatarFile($form->get('username')->getData());
            $user->setRegistrationDate(date_create(date('Y-m-d')));
            $user->setRoles(['ROLE_USER']);
            $user->setIsMuted(0);

	        $em = $this->getDoctrine()->getManager();
	        $em->persist($user);
	        $em->flush();

	        return $this->redirectToRoute('home');
        }
        
        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
