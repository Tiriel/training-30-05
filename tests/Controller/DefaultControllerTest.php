<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function providePulicUrls(): array
    {
        return [
            ['/', 200, true],
            ['/contact', 200],
            ['/hello/John', 200],
            ['/book', 200],
            ['/toto', 404],
        ];
    }

    /**
     * @dataProvider providePulicUrls
     */
    public function testPublicUrls(string $url, int $statusCode): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertResponseStatusCodeSame($statusCode);
    }
}
