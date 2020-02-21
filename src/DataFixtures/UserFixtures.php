<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $currentDate = date_create(date('Y/m/d'));

        $user = new User();
        $user->setUsername('User');
        $user->setRoles(['ROLE_USER']);
        $user->setEmail('user@domain.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user,'fakeuser4'));
        $user->setRegistrationDate($currentDate);
        $user->setIsMuted(0);
        $manager->persist($user);

        $user = new User();
        $user->setUsername('Moderator');
        $user->setRoles(['ROLE_USER','ROLE_MODO']);
        $user->setEmail('modo@domain.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user,'fakeuser4'));
        $user->setRegistrationDate($currentDate);
        $user->setIsMuted(0);
        $manager->persist($user);

        $user = new User();
        $user->setUsername('Admin');
        $user->setRoles(['ROLE_USER','ROLE_MODO','ROLE_ADMIN']);
        $user->setEmail('admin@domain.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user,'fakeuser4'));
        $user->setRegistrationDate($currentDate);
        $user->setIsMuted(0);
        $manager->persist($user);

        $manager->flush();
    }
}
