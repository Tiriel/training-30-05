<?php

namespace App\Transformer;

use App\Entity\Genre;
use App\Entity\Movie;
use App\Repository\GenreRepository;

class MovieTransformer
{
    private GenreRepository $repository;

    public function __construct(GenreRepository $repository)
    {
        $this->repository = $repository;
    }

    public function arrayToMovie(array $data): Movie
    {
        $movie = (new Movie())
            ->setTitle($data['Title'])
            ->setPoster($data['Poster'])
            ->setReleasedAt(new \DateTimeImmutable($data['Released']))
            ->setCountry($data['Country'])
            ->setPrice(5.0)
            ;
        $dGenres = explode(', ', $data['Genre']);
        foreach ($dGenres as $dGenre) {
            $genre = $this->repository->findOneBy(['name' => $dGenre]) ?? (new Genre())->setName($dGenre);
            $movie->addGenre($genre);
        }

        return $movie;
    }
}