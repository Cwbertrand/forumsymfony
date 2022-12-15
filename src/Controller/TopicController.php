<?php

namespace App\Controller;

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
    //     $topic = $this->em->getRepository(Topic::class)->findBy([], ['topcreateAt' => 'ASC']);
    //     dd($topic);
    //     return $this->render('topic/index.html.twig', [
    //         'forms' => $topic,
    //     ]);
    // }

    #[Route('/topic/add', name: 'add_topic')]
    public function addTopic(Topic $topic = null, Request $request)
    {
        $form = $this->createForm(TopicType::class, $topic);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $topic = $form->getData();
            $this->em->persist($topic);
            $this->em->flush();

            return $this->redirectToRoute('topic');
        }
        return $this->render('topic/addtopic.html.twig', [
            'form' => $form->createView()
        ]);
    }


    //show detail messages under a topic
    #[Route('/topic/post/{id}', name: 'show_message')]
    public function showPost(Post $post = null, User $user = null, Topic $topic = null, Request $request)
    {

        //dd($topic);
        $post = new Post();
        $commentform = $this->createForm(PostType::class, $post);
        //dd($commentform);
        $commentform->handleRequest($request);
        if($commentform->isSubmitted() && $commentform->isValid()){
            $post = $commentform->getData();
            $post->setCreateAt(new DateTime());
            $post->setTopic($topic);
            $this->em->persist($post);
            $this->em->flush();
        }

        return $this->render('topic/showmessage.html.twig', [
            'commentform' => $commentform->createView(),
            'detailpost' => $topic,
        ]);
    }

    // //show detail messages under a topic
    // #[Route('/post/add', name: 'add_message')]
    // public function addMessage(Post $post = null, Request $request)
    // {

    //     $form = $this->createForm(PostType::class, $post);
    //     $form->handleRequest($request);

    //     if($form->isSubmitted() && $form->isValid()){
    //         $post = $form->getData();
    //         $this->em->persist($post);
    //         $this->em->flush();

    //         return $this->redirectToRoute('add_category');
    //     }

    //     return $this->render('topic/addmessage.html.twig', [
    //         'form' => $form->createView()
    //     ]);
    // }

}
