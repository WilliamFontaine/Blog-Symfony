<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CategoryType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    #[Route(path: '/', name: 'app_blog_home')]
    #[Route('/blog', name: 'app_blog')]
    public function index(ArticleRepository $repo): Response
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $repo->findAll(),
        ]);
    }

    #[Route('/article/{id}/edit', name: "app_blog_edit") ]
    #[Route('/article/new', name: "app_blog_create")]
    public function create(Article $article = null, Request $request, EntityManagerInterface $manager): Response
    {
        if (!$article) {
            $article = new Article();
        }
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$article->getId()) {
                $article->setAuthor($this->getUser());
                $article->setCreatedAt(new DateTime());
            } else {
                $article->setModifiedAt(new DateTime());
            }
            $manager->persist($article);
            $manager->flush();
            return $this->redirectToRoute('app_blog_show', ['id' => $article->getId()]);
        }
        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId()
        ]);
        return $this->redirectToRoute('app_security_login');
    }

    #[Route("/article/{id}", name: 'app_blog_show')]
    public function show(Article $article, Request $request, EntityManagerInterface $manager)
    {
        $comment = new Comment();
        if ($this->getUser()) {
            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setCreatedAt(new DateTime());
                $comment->setArticle($article);
                $comment->setAuthor($this->getUser());
                $manager->persist($comment);
                $manager->flush();
                return $this->redirectToRoute('app_blog_show', ['id' => $article->getId()]);
            }
            return $this->render('blog/show.html.twig', [
                'article' => $article,
                'formComment' => $form->createView(),
            ]);
        }
        return $this->render('blog/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route("/article/{id}/delete", name: 'app_blog_delete')]
    public function delete(Article $article, EntityManagerInterface $manager)
    {
        if ($this->getUser() === $article->getAuthor()) {
            $manager->remove($article);
            $manager->flush();
            return $this->redirectToRoute('app_blog');
        }
        return $this->redirectToRoute('app_security_login');
    }

    #[Route("/category/new", name: 'app_blog_category')]
    public function category(Category $category = null, Request $request, EntityManagerInterface $manager)
    {
        if ($category === null) {
            $category = new Category();
        }
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute('app_blog_home');
        }
        return $this->render('blog/category.html.twig', [
            'formCategory' => $form->createView(),
            'editMode' => $category->getId()
        ]);
    }
}
