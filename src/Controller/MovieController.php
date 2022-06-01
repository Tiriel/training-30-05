<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Provider\MovieProvider;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/movie", name="app_movie_")
 */
class MovieController extends AbstractController
{
    /**
     * @Route("", name="index")
     */
    public function index(): Response
    {
        return $this->render('movie/index.html.twig', [
            'controller_name' => 'MovieController',
        ]);
    }

    /**
     * @Route("/{title}", name="details")
     */
    public function details(string $title, MovieRepository $repository, MovieProvider $provider): Response
    {
        $movie = $repository->findOneBy(['title' => $title]) ?? $provider->getMovieByTitle($title);

        if (!$movie->getId()) {
            $repository->add($movie, true);
        }

        return $this->render('movie/details.html.twig', [
            'movie' => $movie,
        ]);
    }
}
