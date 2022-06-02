<?php

namespace App\Event;

use App\Entity\Movie;
use Symfony\Contracts\EventDispatcher\Event;

class MovieOrderEvent extends Event
{
    public const NAME = 'movie.order';
    private Movie $movie;

    public function __construct(Movie $movie)
    {
        $this->movie = $movie;
    }

    public function getMovie(): Movie
    {
        return $this->movie;
    }
}