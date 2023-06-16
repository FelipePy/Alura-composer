<?php

namespace Felipepy\SearchEngineCourses;

use GuzzleHttp\ClientInterface;
use Symfony\Component\DomCrawler\Crawler;

class SearchEngine
{
    /**
     * @var ClientInterface
     */
    private $httpClient;
    /**
     * @var Crawler
     */
    private $crawler;


    public function __construct(ClientInterface $httpClient, Crawler $crawler)
    {
        $this->crawler = $crawler;
        $this->httpClient = $httpClient;
    }

    public function search(string $url): array
    {
        $response = $this->httpClient->request('GET', $url);
        $html = $response->getBody();

        $this->crawler->addHtmlContent($html);
        $elementsCourses = $this->crawler->filter('span.card-curso__nome');
        $courses = [];

        foreach ($elementsCourses as $element) {
            $courses[] = $element->textContent;
        }

        return $courses;
    }
}
