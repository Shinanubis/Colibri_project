<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('backoffice/dashboard/dashboard.html.twig', [
            'countArticles' => $this->getDoctrine()->getRepository(Article::class)->countArticles(),
            'countComments' => $this->getDoctrine()->getRepository(Comment::class)->countComments(),
            'countUsers' => $this->getDoctrine()->getRepository(User::class)->countUsers()
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle('Colibri Project');

    }

    public function configureMenuItems(): iterable
    {

        yield MenuItem::linktoUrl('Blog', 'fa fa-home','/');
        yield MenuItem::section('User');
        yield MenuItem::linkToCrud('User', 'fas fa-user', User::class);
        yield MenuItem::section('Article');
        yield MenuItem::linkToCrud('Article', 'fas fa-newspaper', Article::class);
        yield MenuItem::linkToCrud('Comment', 'fas fa-comments', Comment::class);
        yield MenuItem::linkToCrud('Tag', 'fas fa-tags', Tag::class);
    }
}
