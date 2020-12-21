<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="homepage", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $data = $this->getDoctrine()->getRepository(Article::class)->findBy([],['created_at' => 'desc']);

        $articles = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1), // number of the current page, pass in the URL, 1 if neither
            4 // number of result per page
        );

        return $this->render('blog/home/index.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("article/{slug}", name="article_show", methods={"GET", "POST"})
     */
    public function show(Article $article): Response
    {
        return $this->render('blog/article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("article/{slug}/comment/new", name="comment_new", methods={"POST"})
     */
    public function newComment(Request $request, Article $article): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $article->addComment($comment);
            $user->addComment($comment);

            $em->persist($comment);
            $em->flush();
        }

        return $this->redirectToRoute('article_show', ['slug' => $article->getSlug()]);
    }


    public function commentForm(Article $article): Response
    {
        $form = $this->createForm(CommentType::class);

        return $this->render('blog/article/_comment_form.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    public function searchBar()
    {
        $form = $this->createFormBuilder(null)
                    -> add('search', TextType::class)
                    -> getForm();

        return $this->render("blog/search_bar.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/search")
     */

    public function handleSearch(Request $request, PaginatorInterface $paginator){
        $search = $request->request->get("form")["search"];
        $data = $this->getDoctrine()->getRepository(Article::class)->findByTitlePart($search,['created_at' => 'desc']);
        $articles = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1), // number of the current page, pass in the URL, 1 if neither
            4 // number of result per page
        );
        return $this->render('blog/home/index.html.twig', [
            'articles' => $articles
        ]);
        //echo $request->request->get("form")["search"];
        //die();
    }
}