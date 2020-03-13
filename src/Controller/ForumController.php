<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Topic;
use App\Repository\TopicRepository;

use App\Entity\Message;
use App\Repository\MessageRepository;

use App\Form\NewMessageType;

use App\Service\UserFunctions;

class ForumController extends AbstractController
{
    /**
     * @Route("/forum", name="forum")
     */
    public function index(TopicRepository $topicRepository, MessageRepository $messageRepository)
    {
        //$topics = $topicRepository->findAll();
        $topics = $topicRepository->getTopicsData();
        foreach ($topics as $key => $value) {
            $countMessage = $messageRepository->getCountMessage($topics[$key]['id']);
            $topics[$key]['countMessage'] = $countMessage;

            $lastMessage = $messageRepository->getLastMessage($topics[$key]['id']);
            $topics[$key]['lastMessage'] = $lastMessage;
        }

        return $this->render('forum/index.html.twig', [
            'topics' => $topics,
        ]);
    }

    /**
     * @Route("/forum/topic/{id}", name="topic")
     */
    public function topic(Topic $topic,Message $message,TopicRepository $topicRepository, MessageRepository $messageRepository, UserFunctions $functions, Request $request, EntityManagerInterface $manager)
    {
        $messages = $messageRepository->getMessages($topic->getId());
        foreach ($messages as $key => $value) {
            $messages[$key]['roles'] = $functions->roleStr(end($messages[$key]['roles']));
        }

        $form = $this->createForm(NewMessageType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $message = new Message();
            $message->setIdTopic($topic->getId());
            $message->setIdUser($this->getUser()->getId());
            $message->setPublicationDate(date_create(date('Y-m-d H:i:s')));
            $message->setContent($form->get('content')->getData());
            $message->setVisible(1);

            $manager->persist($message);
            $manager->flush();

            return $this->redirectToRoute('topic', ['id' => $topic->getId()]);
        }

        
        return $this->render('forum/topic.html.twig', [
            'topic' => $topic,
            'messages' => $messages,
            'form'  =>  $form->createView()
        ]);
    }

    /**
     * @Route("/forum/editMessage/{id}", name="editMessage")
     */
    public function editMessage(Message $message, MessageRepository $messageRepository , Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(NewMessageType::class,$message);
        $form->handleRequest($request);
        $topic = $messageRepository->getTopicData($message->getIdTopic());

        if($form->isSubmitted() && $form->isValid()){
            $message->setContent($form->get('content')->getData());
            $message->setEdited(TRUE);
            $manager->persist($message);
            $manager->flush();

            return $this->redirectToRoute('topic', ['id' => $message->getIdTopic()]);
        }

        return $this->render('forum/editMessage.html.twig', [
            'topic' => $topic,
            'message' => $message,
            'form'  =>  $form->createView()
        ]);
    }
}
