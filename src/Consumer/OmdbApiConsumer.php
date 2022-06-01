<?php

namespace App\Consumer;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OmdbApiConsumer
{
    public const TYPE_TITLE = 'title';
    public const TYPE_ID = 'id';
    public const SEARCH_TYPES = [
        self::TYPE_TITLE => 't',
        self::TYPE_ID => 'i',
    ];

    private HttpClientInterface $omdbClient;

    public function __construct(HttpClientInterface $omdbClient)
    {
        $this->omdbClient = $omdbClient;
    }

    public function getMovieByTitle(string $title): array
    {
        return $this->get('title', $title);
    }

    public function getMovieById(string $id): array
    {
        return $this->get('id', $id);
    }

    private function get(string $type, string $value): array
    {
        if (!array_key_exists($type, self::SEARCH_TYPES)) {
            throw new \InvalidArgumentException();
        }

        return $this->omdbClient->request(
            Request::METHOD_GET,
            '',
            [
                'query' => [
                    self::SEARCH_TYPES[$type] => $value
                ]
            ]
        )->toArray();
    }
}