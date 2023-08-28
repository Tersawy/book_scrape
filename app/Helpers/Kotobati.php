<?php

namespace App\Helpers;

use Exception;

use Symfony\Component\DomCrawler\Crawler;

use GuzzleHttp\Client;

use GuzzleHttp\Promise\Utils;

use GuzzleHttp\Exception\ServerException;

class Kotobati
{
    private $client;
    private $clientOptions;
    private $baseUrl = 'https://www.kotobati.com';
    public $books = [];

    function __construct()
    {
        $headers = ['User-Agent' => $this->getRandomUserAgent()];

        $this->clientOptions = ['headers' => $headers];

        $this->randomUserAgent();

        $this->client = new Client($this->clientOptions);
    }

    public function getRandomBooks($num = 10)
    {
        try {
            $response = $this->client->get($this->baseUrl);

            $html = $response->getBody()->getContents();

            $crawler = new Crawler($html);

            $crawler->filter(".views-infinite-scroll-content-wrapper .book-box")->each(function ($node) use ($num) {
                if (count($this->books) > $num) return;

                $this->addBook($node);
            });

            return $this;
        } catch (ServerException $e) {
            throw new Exception('Something went wrong when trying to get books!');
        }
    }

    private function addBook(Crawler $bookNode)
    {
        $title = $this->getCrawlerText($bookNode, '.title a');

        $pageLink = $this->getCrawlerAttr($bookNode, '.title a', 'href');

        $bookId = $this->getCrawlerAttr($bookNode, '.rating-favourites a.add-to-fav', 'data-id');

        if (!$title || !$pageLink || !$bookId) return $this;

        $this->books[] = [
            'book_id' => $bookId,

            'title' => $title,

            'author' => $this->getCrawlerText($bookNode, '.author-label a'),

            'image' => 'https://www.kotobati.com' . $this->getCrawlerAttr($bookNode, '.book-image img', 'data-src'),

            'source_link' => 'https://www.kotobati.com' . $pageLink,
        ];

        return $this;
    }

    public function loadBooksDetails()
    {
        $promises = [];

        // Create concurrent requests
        foreach ($this->books as &$book) {
            $this->randomUserAgent();

            $promises[] = $this->client->getAsync($book['source_link'], $this->clientOptions);
        }

        // Wait for all promises to complete and get their results
        $results = Utils::unwrap($promises);

        // Create concurrent requests
        foreach ($results as $result) {
            $html = $result->getBody()->getContents();

            $bookPageCrawler = new Crawler($html);

            $this->setBookDetail($bookPageCrawler);
        }

        return $this;
    }

    private function setBookDetail(Crawler $bookPageCrawler)
    {

        $bookId = $this->getCrawlerAttr($bookPageCrawler, '#block-ktobati-content article', 'data-history-node-id');

        foreach ($this->books as &$book) {
            if ($book['book_id'] !== $bookId) continue;

            $book['pages_count'] = $this->getCrawlerText($bookPageCrawler, '.book-table-info li:nth-child(1) .numero');

            $book['lang'] = $this->getCrawlerText($bookPageCrawler, '.book-table-info li:nth-child(2) p:nth-child(2)');

            $book['size'] = $this->getCrawlerText($bookPageCrawler, '.book-table-info li:nth-child(3) p:nth-child(2)');

            $pdfReadLink = $this->getCrawlerAttr($bookPageCrawler, '.info .detail-box a.read', 'href');

            if (!is_null($pdfReadLink)) {
                $pdfDownloadId = str_replace('/book/reading/', '', $pdfReadLink);

                $book['download_link']  = 'https://www.kotobati.com/book/download/' . $pdfDownloadId;
            } else {
                $book['download_link']  = null;
            }
        }

        return $this;
    }

    private function getRandomUserAgent()
    {
        return BrowserHelper::randomUserAgent();
    }

    private function randomUserAgent()
    {
        $this->clientOptions['headers']['User-Agent'] = $this->getRandomUserAgent();

        return $this;
    }

    private function getCrawlerText(Crawler $crawler, string $selector, $default = null)
    {
        try {
            $text = $crawler->filter($selector)->text();

            return $text;
        } catch (\InvalidArgumentException $e) {
            return $default;
        }
    }

    private function getCrawlerAttr(Crawler $crawler, string $selector, string $attr, $default = null)
    {
        try {
            $text = $crawler->filter($selector)->attr($attr);

            return $text;
        } catch (\InvalidArgumentException $e) {
            return $default;
        }
    }
}
