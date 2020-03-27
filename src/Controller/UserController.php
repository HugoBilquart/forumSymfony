<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

use App\Entity\User;
use App\Repository\UserRepository;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;

use App\Form\AvatarType;
use App\Form\RegisterType;
use App\Form\ChangePasswordType;
use App\Form\EditProfileDetailsType;

use App\Service\UserFunctions;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\FormHelper;
use Symfony\Component\VarDumper\VarDumper;

class UserController extends AbstractController
{
    /**
     * @Route("/users", name="users")
     */
    public function index(UserRepository $userRepository)
    {
        $count = $userRepository->countUser();
        if($count <= 10) {
            $users = $userRepository->findAll();
            $countPage = null;
        }
        else {
            $users = $userRepository->getUsers(1);
            $countPage = $userRepository->countPage();
        }   
        return $this->render('user/index.html.twig', [
            'users' => $users,
            'countPage' => $countPage,
            'page' => 1
        ]);
    }

    /**
     * @Route("/users?page={page}", name="users_page")
     */
    public function indexPage(UserRepository $userRepository,$page)
    {
        $users = $userRepository->getUsers($page);
        $countPage = $userRepository->countPage();

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'countPage' => $countPage,
            'page' => $page
        ]);
    }

    /**
     * @Route("/user/{id}", name="profile", requirements={"id"="\d+"})
     */
    public function profile(User $user = null)
    {
        if (empty($user)) {
            return $this->render('exceptions/404.html.twig', [
                'reason' => 'user'
            ]);
        }
        else {
            return $this->render('user/profile.html.twig', [
                'data' => $user
            ]);
        }
    }

    /**
     * @Route("/editProfile/details/{id}", name="editProfileDetails")
     */
    public function details(User $user, Request $request, EntityManagerInterface $manager)
    {
        if($this->getUser() && $this->getUser() == $user) {
            $form = $this->createForm(EditProfileDetailsType::class, $user);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $loggedUser = $this->getUser();
                $loggedUser->setBirthDate($form->get('birth_date')->getData());
                $loggedUser->setCountry($form->get('country')->getData());
                $loggedUser->setBiography($form->get('biography')->getData());
                $loggedUser->setSignature($form->get('signature')->getData());

                $manager->persist($loggedUser);
                $manager->flush();
    
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
                    'id' => $this->getUser()->getId(),
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
     * @Route("/editAccount/password/{id}", name="editPassword")
     */
    public function password(User $user, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder, UserFunctions $userFunctions)
    {
        if($this->getUser() && $this->getUser() == $user) {
            $form = $this->createForm(ChangePasswordType::class);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $postPassword = $passwordEncoder->encodePassword($user,$form->get('currentPassword')->getData());

                $oldPassword = $form->get('currentPassword')->getData();

                // Si l'ancien mot de passe est bon
                if ($passwordEncoder->isPasswordValid($user, $oldPassword)) {
                    if(!preg_match('^\S*(?=\S{8,})(?=\S*[\d])\S*$^',$form->get('newPassword')->getData())) {
                        $form->get('newPassword')->addError(new FormError("You're password isn't valid (At least 8 characters and at least 1 number)"));
                        return $this->render('user/changePassword.html.twig', [
                            'form'  => $form->createView()
                        ]);
                    }
                    else {
                        $newEncodedPassword = $passwordEncoder->encodePassword($user, $form->get('newPassword')->getData()); 
                        $user->setPassword($newEncodedPassword);
                        $manager->persist($user);
                        $manager->flush();

                        return $this->render('user/changePassword.html.twig');
                    }
                }
                else {                    
                    $form->get('currentPassword')->addError(new FormError("Entered password doesn't match to your current password"));

                    return $this->render('user/changePassword.html.twig', [
                        'form'  => $form->createView()
                    ]);
                }
            }

            return $this->render('user/changePassword.html.twig', [
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
