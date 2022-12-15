<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Topic;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $category = $this->em->getRepository(Category::class)->findBy([], ['nomcategory' => 'ASC']);
        return $this->render('home/index.html.twig', [
            'forms' => $category,
        ]);
    }

    #[Route('/add/category', name: 'add_category')]
    #[Route('/category/{id}/edit', name: 'edit_category')]
    public function addCategory(Category $category = null, Request $request)
    {
        if(!$category){
            $category = new Category();
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $category = $form->getData();
            $this->em->persist($category);
            $this->em->flush();

            return $this->redirectToRoute('add_category');
        }

        return $this->render('home/addcategory.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/show/topics/{id}', name: 'show_topic')]
    public function showTopic(Category $category): Response
    {
        
        return $this->render('topic/index.html.twig', [
            'detailcategory' => $category,
        ]);
    }


}
