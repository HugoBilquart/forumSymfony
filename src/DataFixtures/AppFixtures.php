<?php

//php bin/console doctrine:fixtures:load --purge-with-truncate

namespace App\DataFixtures;

use App\Entity\Topic;
use App\Entity\Message;
use App\Entity\User;

use App\Repository\UserRepository;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Faker;

class AppFixtures extends Fixture
{
    public function createAvatarFile($username,$role) {
        switch($role) {
            case 'user':
                $file = 'public/img/users/saves/default.png';
                break;
            case 'modo':
                $file = 'public/img/users/saves/Moderator.png';
                break;
            case 'admin':
                $file = 'public/img/users/saves/Admin.png';
                break;
        }
        $newfile = 'public/img/users/'.$username.'.png';
        if (!copy($file, $newfile)) {
            echo "Failed to create new user avatar\n";
        }
        else {
            return $newfile;
        }
    }

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        echo "\033[97mLet's fill the forum :\033[0m\n";
        define("TOPIC_COUNT", 200);
        define("USER_COUNT", 20);

        //STEP 1 : Clear users avatar directory
        $files = glob('public/img/users/*.png');
        foreach($files as $file){
            if(is_file($file))
            unlink($file);
        }
        echo "\033[33m > \033[32mUser pictures directory cleared\033[0m\n";


        $faker = Faker\Factory::create('en_US');
        $currentDate = date_create(date('Y/m/d'));
        $currentDatetime = date_create(date('Y-m-d H:i:s'));

        //STEP 2 : Create 11 users, 1 moderator and 1 administrator
        $user = new User();
        $user->setUsername('User');
        $this->createAvatarFile('User','user');
        $user->setRoles(['ROLE_USER']);
        $user->setEmail('user@domain.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user,'fakeuser4'));
        $user->setRegistrationDate($currentDate);
        $user->setIsMuted(0);
        $manager->persist($user);

        $user = new User();
        $user->setUsername('Moderator');
        $this->createAvatarFile('Moderator','modo');
        $user->setRoles(['ROLE_USER','ROLE_MODO']);
        $user->setEmail('modo@domain.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user,'fakemodo4'));
        $user->setRegistrationDate($currentDate);
        $user->setIsMuted(0);
        $manager->persist($user);

        $user = new User();
        $user->setUsername('Admin');
        $this->createAvatarFile('Admin','admin');
        $user->setRoles(['ROLE_USER','ROLE_MODO','ROLE_ADMIN']);
        $user->setEmail('admin@domain.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user,'fakeadmin4'));
        $user->setRegistrationDate($currentDate);
        $user->setIsMuted(0);
        $manager->persist($user);

        for($i = 1; $i <= USER_COUNT; $i++) {
            $user = new User();
            $username = $faker->firstName();
            $user->setUsername($username);
            $this->createAvatarFile($username,'user');
            $user->setRoles(['ROLE_USER']);
            $user->setEmail(strtolower($username).'@domain.com');
            $user->setPassword($this->passwordEncoder->encodePassword($user,'fakeuser4'));
            $user->setRegistrationDate($currentDate);
            $user->setIsMuted(rand(0,1));
            $manager->persist($user);

            if($i == USER_COUNT) {
                echo "\033[33m > Create ".USER_COUNT." users : ".round($i/(USER_COUNT / 100))." %   \033[0m\n";
            }
            else {
                echo "\033[33m > Create ".USER_COUNT." users : ".round($i/(USER_COUNT / 100))." %   \033[0m\r";
            }
        }
        $manager->flush();
        echo "\033[33m > \033[32m".(USER_COUNT + 1)." users, 1 moderator and 1 administrator created\033[0m\n";

        //STEP 3 : Create 50 topics with 5 - 30 messages
        $users = $manager->getRepository(User::class)->findAll();

        //TODO : Get user by roles (users,moderators,administrator)
        //$modos = $manager->getRepository(User::class)->findBy(['roles' => '%"ROLE_MODO"%']);
        //$admin = $manager->getRepository(User::class)->findBy(['roles' => '%"ROLE_ADMIN"%']);

        for ($i = 0; $i < 200; $i++) { 
            //Create topic
            //TODO : If author is a moderator or an administrator, staffOnly and/or readOnly options can be enabled --> rand(0,1)
            $topic = new Topic();
            $topic->setName($faker->text($maxNbChars = rand(30,100)));
            $topic->setCreationDate($currentDate);
            $topic->setReadOnly(0);
            $topic->setStaffOnly(0);
            $topic->setComplete(rand(0,1));
            $topic->setVisible(1);
            $topic->setAuthor(array_rand($users));
            $manager->persist($topic);
            $manager->flush();

            //Write first message (by author, always visible)
            $message = new Message();
            $message->setIdTopic($topic->getId());
            $message->setIdUser($topic->getAuthor());
            $message->setPublicationDate($currentDatetime);
            $message->setContent($faker->text($maxNbChars = rand(50,500)));
            $message->setVisible(1);
            $manager->persist($message);

            //Generate 5-30 random messages in the topic
            //TODO : staffOnly and/or readOnly options of the topic will influence who will write messages
            for($j = 0; $j < rand(5,30); $j++) {
                $message = new Message();
                $message->setIdTopic($topic->getId());
                $message->setIdUser(array_rand($users));
                $message->setPublicationDate($currentDatetime);
                $message->setContent($faker->text($maxNbChars = rand(50,500)));
                $message->setVisible(rand(0,1));
                $manager->persist($message);
            }
            $manager->flush();

            //Loading during topic creation progression
            if($i == TOPIC_COUNT - 1) {
                echo "\033[33m > Create ".TOPIC_COUNT." topic : ".round($i/(TOPIC_COUNT / 100))." %   \033[0m\n";
            }
            else {
                echo "\033[33m > Create ".TOPIC_COUNT." topic : ".round($i/(TOPIC_COUNT / 100))." %   \r";
            }
        }
        echo "\033[33m > \033[32m".TOPIC_COUNT." topics created\033[0m\n";
    }
}
