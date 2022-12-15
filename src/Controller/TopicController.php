<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Topic;
use App\Entity\User;
use App\Form\PostType;
use App\Form\TopicType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TopicController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    // #[Route('/topic', name: 'topic')]
    // public function index(): Response
    // {
    //     return $this->render('topic/index.html.twig', [
    //     ]);
    // }

    #[Route('/topic/add', name: 'add_topic')]
    #[Route('/topic/{id}/edit', name: 'edit_topic')]
    public function addTopic(Topic $topic = null, Request $request)
    {
        if (!$topic) {
            $topic = new Topic();
        }

        $form = $this->createForm(TopicType::class, $topic);
        $form->handleRequest($request);

        $user = $this->getUser();
        if($form->isSubmitted() && $form->isValid()){
            $topic = $form->getData();
            $topic->setTopcreateAt(new DateTime());
            $topic->setUser($user);


            $this->em->persist($topic);
            $this->em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('topic/addtopic.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    //show detail messages under a topic
    #[Route('/topic/post/{id}', name: 'show_message')]
    #[Route('/topic/post/{id}/edit', name: 'edit_message')]
    public function showPost(Post $post = null, Topic $topic = null, Request $request)
    {
        if (!$post) {
            $post = new Post();
        }

        //dd($topic);
        $post = new Post();
        $user = $this->getUser();
        $commentform = $this->createForm(PostType::class, $post);
        $commentform->handleRequest($request);
        if($commentform->isSubmitted() && $commentform->isValid()){
            $post = $commentform->getData();
            $post->setCreateAt(new DateTime());
            $post->setTopic($topic);
            $post->setUser($user);
            
            $this->em->persist($post);
            $this->em->flush();
        }

        return $this->render('topic/showmessage.html.twig', [
            'commentform' => $commentform->createView(),
            'detailpost' => $topic,
        ]);
    }

}
