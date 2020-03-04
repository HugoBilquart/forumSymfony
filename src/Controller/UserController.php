<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\User;
use App\Repository\UserRepository;

use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;

use App\Form\RegisterType;
use App\Form\AvatarType;
use App\Form\EditProfileDetailsType;

use App\Service\UserFunctions;

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
                    'id' => $user->getId(),
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
     * @Route("/editProfile/avatar/{id}", name="changeAvatar")
     */
    public function avatar(User $user, Request $request, UserFunctions $userFunctions)
    {
        if($this->getUser() && $this->getUser() == $user) {
            $form = $this->createForm(AvatarType::class);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $userFunctions->changeAvatar($form->get('file')->getData(),$user);
    
                return $this->redirectToRoute('profile', array(
                    'id' => $this->getUser(),
                ));
            }

            return $this->render('user/changeAvatar.html.twig', [
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
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer)
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
            
            /*$message = (new \Swift_Message('IT Solutions - New account created'))
            ->setFrom('send@example.com')
            ->setTo('zerrouk99@gmail.com')
            ->setBody('An account for <b>'.$user->getUsername().'</b> has been created.');
            ;

            $mailer->send($message);*/

	        return $this->render('user/registered.html.twig', [
                'user' => $user
            ]);
        }
        else {
            return $this->render('user/register.html.twig', [
                'form' => $form->createView()
            ]);
        }
        
        /*return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);*/
    }

    /**
     * @Route("/mail", name="mail")
     */
    public function mail(\Swift_Mailer $mailer)
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('send@example.com')
            ->setTo('zerrouk99@gmail.com')
            ->setBody('You should see me from the profiler!')
        ;


        $mailer->send($message);

        return $this->render('user/mail.html.twig');
    }
}
