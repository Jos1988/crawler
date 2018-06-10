<?php

namespace App;

use App\Entity\CrawlLink;
use App\Exceptions\CrawlException;
use DOMElement;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Spider
 */
class Spider
{
    /**
     * @var CrawlLink
     */
    private $crawlLink;

    /**
     * @var Crawler
     */
    private $crawler;

    public function __construct(CrawlLink $crawlLink)
    {
        $this->crawlLink = $crawlLink;
    }

    /**
     * Crawl a web page, and store crawler.
     *
     * @return Crawler
     */
    public function crawl(): Crawler
    {
        if (null === $this->crawlLink) {
            throw new CrawlException('Crawl link not set on Spider');
        }

        $curlHandler = curl_init($this->crawlLink->getAbsoluteLink());
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandler, CURLOPT_FOLLOWLOCATION, true);
        $curlReturn = curl_exec($curlHandler);
        if (false === $curlReturn) {
            $errorMessage = sprintf('Curl returned error message: %s', curl_error($curlHandler));
            throw new CrawlException($errorMessage, 500);
        }

        $this->crawler = new Crawler($curlReturn);

        return $this->crawler;
    }

    /**
     * Get links from crawled page.
     *
     * @return array
     */
    protected function getLinks()
    {
        if (null === $this->crawler) {
            throw new CrawlException('Spider hasn\'t crawled yes');
        }

        $crawler = $this->crawler->filter('a');

        $links = [];
        /** @var DOMElement $element */
        foreach ($crawler as $element) {
            $url = $element->getAttribute('href');
            $links[] = $url;
        }

        return $links;
    }

    /**
     * @return array|CrawlLink[]
     */
    public function getUniqueCrawlLinks(): array
    {
        $urls = array_unique($this->getLinks());
        $crawlLinks = [];
        foreach ($urls as $url) {
            $crawlLink = new CrawlLink();
            $crawlLink->setLink($url)
                ->setWebsite($this->crawlLink->getWebsite());

            $crawlLinks[] = $crawlLink;
        }

        return $crawlLinks;
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

