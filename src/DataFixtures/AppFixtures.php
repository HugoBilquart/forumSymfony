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

use Symfony\Component\Console\Helper\ProgressBar;

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

    function generateUsername(ObjectManager $manager) {
        $available = FALSE;
        while($available == FALSE) {
            $faker = Faker\Factory::create('en_US');
            $username = $faker->firstName();
            $found = $manager->getRepository(User::class)->findBy(['email' => $username.'@domain.com']);

            if(count($found) == 0) {
                $available = TRUE;
            }
        }
        return $username;
    }

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        echo "This fixture will fill the forum by creating a specified number of random user (In addition to a defined user, a moderator and an administrator). Then it will create a specified number of topics and messages wrote by those random users.\n";
        echo "\033[97mLet's fill the forum :\033[0m\n";
        $userCount = readline("How many user do you want to create (Only integer) ? ");

        if(!is_numeric($userCount) || strpos( $userCount, "." ) !== false) {
            echo "\033[33m > \033[31mError : User count must be int without decimal\033[0m\n";
            exit();
        }

        $topicCount = readline("How many topic do you want to create (Only integer) ? ");
        if(!is_numeric($topicCount) || strpos( $topicCount, "." ) !== false) {
            echo "\033[33m > \033[31mError : Topic count be int without decimal\033[0m\n";
            exit();
        }

        define("TOPIC_COUNT", $topicCount);
        define("USER_COUNT", $userCount);

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

        //STEP 2 : Create 1 user, 3 moderators, 1 administrator and defined number of user
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
        $user->setUsername('Guardian');
        $this->createAvatarFile('Guardian','modo');
        $user->setRoles(['ROLE_USER','ROLE_MODO']);
        $user->setEmail('guardian@domain.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user,'fakemodo4'));
        $user->setRegistrationDate($currentDate);
        $user->setIsMuted(0);
        $manager->persist($user);

        $user = new User();
        $user->setUsername('Protection');
        $this->createAvatarFile('Protection','modo');
        $user->setRoles(['ROLE_USER','ROLE_MODO']);
        $user->setEmail('protection@domain.com');
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
            $username = $this->generateUsername($manager);
            $user->setUsername($username);
            $this->createAvatarFile($username,'user');
            $user->setRoles(['ROLE_USER']);
            $user->setEmail(strtolower($username).'@domain.com');
            $user->setPassword($this->passwordEncoder->encodePassword($user,'fakeuser4'));
            $user->setRegistrationDate($currentDate);
            $user->setIsMuted(rand(0,1));
            $user->setBirthDate(date_create($faker->date($format = 'Y-m-d', $max = 'now')));
            $manager->persist($user);
            $manager->flush();

            if($i == USER_COUNT) {
                echo "\033[33m > Create ".USER_COUNT." users : ".round($i/(USER_COUNT / 100))." % \033[0m\n";
            }
            else {
                echo "\033[33m > Create ".USER_COUNT." users : ".round($i/(USER_COUNT / 100))." % \033[0m\r";
            }
        }
        echo "\033[33m > \033[32m".(USER_COUNT + 1)." users, 1 moderator and 1 administrator created\033[0m\n";

        //STEP 3 : Create 50 topics with 5 - 30 messages

        //Create group of users depending of their role
        $admin = $manager->getRepository(User::class)->getByRole('"ROLE_USER", "ROLE_MODO", "ROLE_ADMIN"');
        $staff = $manager->getRepository(User::class)->getByRole('"ROLE_USER", "ROLE_MODO"');
        $users = $manager->getRepository(User::class)->getByRole('"ROLE_USER"');

        for ($i = 1; $i <= $topicCount; $i++) { 
            //Create topic
            $topic = new Topic();
            $topic->setName($faker->text($maxNbChars = rand(30,100)));
            $topic->setCreationDate($currentDate);
            $topic->setComplete(rand(0,1));
            $topic->setVisible(1);

            //Random user (Select id)
            $author = $users[array_rand($users)]['id'];
            $topic->setAuthor($author);

            //If selected user is admin, readOnly and staffOnly options can be enabled
            if(array_search($author, array_column($admin, 'id')) !== FALSE ) {
                $readOnly = rand(0,1);
                $topic->setReadOnly($readOnly);
                if($readOnly == 1) {
                    $topic->setStaffOnly(0);
                }
                else {
                    $topic->setStaffOnly(rand(0,1));
                }
            }
            //If selected user is staff (moderator), staffOnly option can be enabled
            else if(array_search($author, array_column($staff, 'id')) !== FALSE) {
                $topic->setReadOnly(0);
                $topic->setStaffOnly(rand(0,1));
            }
            else {
                $topic->setReadOnly(0);
                $topic->setStaffOnly(0);
            }

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
            $manager->flush();

            //Generate 5-30 random messages in the topic
            for($j = 0; $j < rand(5,30); $j++) {
                $message = new Message();
                $message->setIdTopic($topic->getId());
                //If read only option is enabled, only author can publish message in the topic
                if($topic->getReadOnly() == 1) {
                    $message->setIdUser($topic->getAuthor());
                }
                else {
                    //If staff only option is enabled, only administrator and moderators can publish message in the topic
                    if($topic->getStaffOnly() == 1) {
                        $message->setIdUser($staff[array_rand($staff)]['id']);
                    }
                    //Else every user can publish message in the topic
                    else {
                        $message->setIdUser($users[array_rand($users)]['id']);
                    }
                }
                $message->setPublicationDate($currentDatetime);
                $message->setContent($faker->text($maxNbChars = rand(50,500)));
                $message->setVisible(rand(0,1));
                $manager->persist($message);
            }

            $manager->flush();

            //Loading during topic creation progression
            if($i == TOPIC_COUNT) {
                echo "\033[33m > Create ".TOPIC_COUNT." topic : ".round($i/(TOPIC_COUNT / 100))." %   \033[0m\n";
            }
            else {
                echo "\033[33m > Create ".TOPIC_COUNT." topic : ".round($i/(TOPIC_COUNT / 100))." %   \r";
            }
        }
        echo "\033[33m > \033[32m".TOPIC_COUNT." topics created\033[0m\n";
    }
}
