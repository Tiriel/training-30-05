<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Event\MovieOrderEvent;
use App\Provider\MovieProvider;
use App\Repository\MovieRepository;
use App\Security\Voter\MovieRatedVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
    public function details(string $title, MovieRepository $repository, MovieProvider $provider, EventDispatcherInterface $dispatcher): Response
    {
        $movie = $repository->findOneBy(['title' => $title]) ?? $provider->getMovieByTitle($title);

        if (!$movie->getId()) {
            $repository->add($movie, true);
        }

        $this->denyAccessUnlessGranted(MovieRatedVoter::RATED, $movie);
        $dispatcher->dispatch(new MovieOrderEvent($movie), MovieOrderEvent::NAME);

        return $this->render('movie/details.html.twig', [
            'movie' => $movie,
        ]);
    }
}
