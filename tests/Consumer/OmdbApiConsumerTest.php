<?php

namespace App\Tests\Consumer;

use App\Consumer\OmdbApiConsumer;
use phpDocumentor\Reflection\Types\Static_;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class OmdbApiConsumerTest extends TestCase
{
    private static OmdbApiConsumer $consumer;
    /**
     * @var MockResponse[]
     */
    private static array $responses;
    private static string $body = <<<EOD
{"Title":"Star Wars","Year":"1977","Rated":"PG","Released":"25 May 1977","Runtime":"121 min","Genre":"Action, Adventure, Fantasy","Director":"George Lucas","Writer":"George Lucas","Actors":"Mark Hamill, Harrison Ford, Carrie Fisher","Plot":"Luke Skywalker joins forces with a Jedi Knight, a cocky pilot, a Wookiee and two droids to save the galaxy from the Empire's world-destroying battle station, while also attempting to rescue Princess Leia from the mysterious Darth ...","Language":"English","Country":"United States","Awards":"Won 6 Oscars. 63 wins & 29 nominations total","Poster":"https://m.media-amazon.com/images/M/MV5BNzg4MjQxNTQtZmI5My00YjMwLWJlMjUtMmJlY2U2ZWFlNzY1XkEyXkFqcGdeQXVyODk4OTc3MTY@._V1_SX300.jpg","Ratings":[{"Source":"Internet Movie Database","Value":"8.6/10"},{"Source":"Rotten Tomatoes","Value":"93%"},{"Source":"Metacritic","Value":"90/100"}],"Metascore":"90","imdbRating":"8.6","imdbVotes":"1,323,906","imdbID":"tt0076759","Type":"movie","DVD":"06 Dec 2005","BoxOffice":"$460,998,507","Production":"N/A","Website":"N/A","Response":"True"}
EOD;
    private static MockHttpClient $client;

    public static function setUpBeforeClass(): void
    {
        static::$responses = [
            new MockResponse(static::$body),
            new MockResponse(static::$body),
        ];
        static::$client = (new MockHttpClient(static::$responses))->withOptions([
            'query' => [
                'apikey' => 'Fak3K3y'
            ]
        ]);
        static::$consumer = new OmdbApiConsumer(static::$client);
    }

    public function testGetMovieById(): void
    {
        $data = static::$consumer->getMovieById('tt0076759');

        $this->assertEquals(200, static::$responses[0]->getStatusCode());
        $this->assertEquals('tt0076759', static::$responses[0]->getRequestOptions()['query']['i']);
        $this->assertEquals('Star Wars', $data['Title']);
    }

    public function testGetMovieByTitle(): void
    {
        $data = static::$consumer->getMovieByTitle('Star Wars');

        $this->assertEquals(200, static::$responses[1]->getStatusCode());
        $this->assertEquals('Star Wars', static::$responses[1]->getRequestOptions()['query']['t']);
        $this->assertEquals('tt0076759', $data['imdbID']);
    }
}
