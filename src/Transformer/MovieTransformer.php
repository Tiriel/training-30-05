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

    public function arrayToMovie(array $data): ?Movie
    {
        if (array_key_exists('Response',$data) && $data['Response'] === false) {
            return null;
        }

        $date = $data['Released'] !== 'N/A' ? $data['Released'] : $data['Year'];
        $movie = (new Movie())
            ->setTitle($data['Title'])
            ->setPoster($data['Poster'])
            ->setReleasedAt(new \DateTimeImmutable($date))
            ->setCountry($data['Country'])
            ->setPrice(5.0)
            ->setImdbId($data['imdbID'])
            ->setRated($data['Rated'])
            ;
        $dGenres = explode(', ', $data['Genre']);
        foreach ($dGenres as $dGenre) {
            $genre = $this->repository->findOneBy(['name' => $dGenre]) ?? (new Genre())->setName($dGenre);
            $movie->addGenre($genre);
        }

        return $movie;
    }
}