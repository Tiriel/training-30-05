<?php

namespace App\Provider;

use App\Consumer\OmdbApiConsumer;
use App\Entity\Movie;
use App\Transformer\MovieTransformer;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MovieProvider
{
    private OmdbApiConsumer $consumer;
    private MovieTransformer $transformer;
    private AuthorizationCheckerInterface $checker;

    public function __construct(OmdbApiConsumer $consumer, MovieTransformer $transformer, AuthorizationCheckerInterface $checker)
    {
        $this->consumer = $consumer;
        $this->transformer = $transformer;
        $this->checker = $checker;
    }

    public function getMovieByTitle(string $title): ?Movie
    {
        return $this->transformer->arrayToMovie(
            $this->consumer->getMovieByTitle($title)
        );
    }
}