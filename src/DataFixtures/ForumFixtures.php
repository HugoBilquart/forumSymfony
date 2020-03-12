<?php

namespace App\DataFixtures;

use App\Entity\Topic;
use App\Entity\Message;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use Faker;

class ForumFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('en_US');

        $currentDate = date_create(date('Y-m-d'));
        $currentDatetime = date_create(date('Y-m-d H:i:s'));

        $topic = new Topic();
        $topic->setName('Fake topic');
        $topic->setAuthor(1);
        $topic->setCreationDate($currentDate);
        $topic->setReadOnly(0);
        $topic->setStaffOnly(0);
        $topic->setComplete(0);
        $topic->setVisible(1);

        $manager->persist($topic);
        $manager->flush();

        for($i = 0; $i < 5; $i++) {
            $message = new Message();
            $message->setIdTopic($topic->getId());
            $message->setIdUser(1);
            $message->setPublicationDate($currentDatetime);
            $message->setContent($faker->realText(rand(200,1000)));
            $message->setVisible(1);
            $manager->persist($message);
        }

        $manager->flush();
    }
}
