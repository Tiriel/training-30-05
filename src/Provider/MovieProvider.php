<?php

namespace App\Provider;

use App\Consumer\OmdbApiConsumer;
use App\Entity\Movie;
use App\Transformer\MovieTransformer;

class MovieProvider
{
    private OmdbApiConsumer $consumer;
    private MovieTransformer $transformer;

    public function __construct(OmdbApiConsumer $consumer, MovieTransformer $transformer)
    {
        $this->consumer = $consumer;
        $this->transformer = $transformer;
    }

    public function getMovieByTitle(string $title): ?Movie
    {
        return $this->transformer->arrayToMovie(
            $this->consumer->getMovieByTitle($title)
        );
    }
}