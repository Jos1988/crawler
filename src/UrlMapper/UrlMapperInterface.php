<?php

namespace App\UrlMapper;

use App\Entity\CrawlLink;
use App\Entity\Website;
use Psr\Log\LoggerInterface;

interface UrlMapperInterface
{
    /**
     * @param array $config
     */
    public function setConfig(array $config): void;

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void;

    /**
     * Gets urls from website pages and updates website until no more new urls are discovered.
     *
     * @param Website         $website
     *
     * @return void
     */
    public function mapWebsiteUrls(Website $website): void;

    /**
     * @param CrawlLink       $crawlLink
     *
     * @return CrawlLink[]
     */
    public function getUrlsFromPage(CrawlLink $crawlLink): array;
}