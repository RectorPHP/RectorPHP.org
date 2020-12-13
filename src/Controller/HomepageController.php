<?php

declare(strict_types=1);

namespace Rector\Website\Controller;

use Rector\Website\Blog\Repository\PostRepository;
<<<<<<< HEAD
use Rector\Website\ValueObject\Routing\RouteName;
=======
use Rector\Website\ValueObject\RouteName;
>>>>>>> b5c3a47... login fixes
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomepageController extends AbstractController
{
    public function __construct(
        private PostRepository $postRepository
    ) {
    }

    #[Route(path: '/', name: RouteName::HOMEPAGE)]
    public function __invoke(): Response
    {
        return $this->render('homepage/homepage.twig', [
            'title' => "We'll Speed up your Development Process by 300 %",
            'last_3_posts' => $this->postRepository->fetchLast(3),
        ]);
    }
}
