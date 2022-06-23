<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(ArticleRepository $repo): Response
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $repo->findAll(),
        ]);
    }

    #[Route("/", name: "app_blog_home")]
    public function home()
    {
        return $this->render('blog/home.html.twig');
    }

    #[Route('/article/new', name: "app_blog_create")]
    #[Route('/article/{id}/edit', name: "app_blog_edit")]
    public function create(Article $article = null, Request $request, EntityManagerInterface $manager)
    {
        if ($this->getUser() !== null) {
            if (!$article) {
                $article = new Article();
            }

            $form = $this->createForm(ArticleType::class, $article);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if (!$article->getId()) {
                    $article->setAuthor($this->getUser()->getUsername());
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
        }
        return $this->redirectToRoute('app_security_login');
    }

    #[Route("/article/{id}", name: 'app_blog_show')]
    public function show(Article $article, Request $request, EntityManagerInterface $manager)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new DateTime());
            $comment->setArticle($article);
            $comment->setAuthor($this->getUser()->getUsername());
            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('app_blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'formComment' => $form->createView(),
        ]);
    }
}
