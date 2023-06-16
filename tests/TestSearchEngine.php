<?php

namespace Felipepy\SearchEngineCourses\Tests;

use Felipepy\SearchEngineCourses\SearchEngine;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\DomCrawler\Crawler;

class TestSearchEngine extends TestCase
{
    private $httpClientMock;
    private $url = 'url-to-test';

    // Preciso criar um mock para o HTML
    // Um mock para o client

    protected function setUp(): void
    {
        $html = <<<FIM
            <html>
                <body>
                    <span class="card-curso__nome">Course 1</span>
                    <span class="card-curso__nome">Course 2</span>
                    <span class="card-curso__nome">Course 3</span>
                </body>
            </html>
        FIM;

        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
            ->method('__toString')
            ->willReturn($html);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient
            ->expects($this->once())
            ->method('request')
            ->with('GET', $this->url)
            ->willReturn($response);

        $this->httpClientMock = $httpClient;
    }

    public function testSearchReturnArrayCourses()
    {
        $crawler = new Crawler();
        $searchEngine = new SearchEngine($this->httpClientMock, $crawler);
        $courses = $searchEngine->search($this->url);

        $this->assertCount(3, $courses);
        $this->assertEquals('Course 1', $courses[0]);
        $this->assertEquals('Course 2', $courses[1]);
        $this->assertEquals('Course 3', $courses[2]);
    }
}
