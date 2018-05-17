<?php

namespace App\Service;

use DOMElement;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Spider
 */
class Spider
{
    /**
     * @var int
     *
     */
    private $id;

    /**
     * @var string
     */
    private $target;

    /**
     * @var Crawler
     */
    private $crawler;

    public function __construct(string $target)
    {
        $this->target = $target;
    }

    /**
     * Crawl a web page, and store crawler.
     *
     * @param string|null $target
     *
     * @return string|Crawler
     */
    public function crawl(string $target = null)
    {
        if (null === $target) {
            $target = $this->target;
        }

        $curlHandler = curl_init($target);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
        $curlReturn = curl_exec($curlHandler);
        if (false === $curlReturn) {
            return curl_error($curlHandler);
        }

        $this->crawler = new Crawler($curlReturn);

        return $this->crawler;
    }

    /**
     * Get links from crawled page.
     *
     * @return array
     */
    public function getLinks()
    {
        if (null === $this->crawler) {
            $this->crawl();
        }

        $crawler = $this->crawler;
        $crawler = $crawler->filter('a');

        $links = [];
        /** @var DOMElement $element */
        foreach ($crawler as $element) {
            $links[] = $element->getAttribute('href');
        }

        return $links;
    }

    /**
     * Get unique links from crawled page.
     *
     * @return array
     */
    public function getUniqueLinks()
    {
        return array_unique($this->getLinks());
    }

    /**
     * Set target
     *
     * @param string $target
     *
     * @return Spider
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * get Crawler
     *
     * @return Crawler
     */
    public function getCrawler(): Crawler
    {
        return $this->crawler;
    }

    /**
     * set Crawler
     *
     * @param Crawler $crawler
     *
     * @return Spider
     */
    public function setCrawler(Crawler $crawler)
    {
        $this->crawler = $crawler;

        return $this;
    }
}

