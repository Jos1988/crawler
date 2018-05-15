<?php

namespace App\Service;

use App\Entity\CrawlLink;
use App\Entity\Website;
use App\Exceptions\CrawlException;
use App\Repository\CrawlLinkRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class SpiderService
{

    /**
     * @var Website
     */
    private $website;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CrawlLinkRepository
     */
    private $crawlLinkRepo;

    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $lastUrl = '';

    /**
     * @var int
     */
    private $lowDistanceUrls = 0;

    /**
     * SpiderService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param array|null             $config
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        array $config = null
    ) {
        $this->entityManager = $entityManager;
        $this->crawlLinkRepo = $this->entityManager->getRepository(CrawlLink::class);
        $this->config = $config;
    }

    /**
     * @param Website $website
     */
    public function setWebsite(Website $website)
    {
        $this->website = $website;
    }

    /**
     * Filter urls and return unique acceptable urls.
     *
     * @param array $urls
     *
     * @return array
     */
    public function filterUrls(array $urls): array
    {
        $approvedUrls = [];
        foreach ($urls as $url) {
            if ($this->checkUrl($url)) {
                $approvedUrls[] = $url;
            }
        }

        $newUrls = [];
        foreach ($approvedUrls as $url) {
            if ($this->checkUrlNew($this->website->getCrawlLinks(), $url)) {
                $newUrls[] = $url;
            }
        }

        if (true === $this->config['trimGET']) {
            $trimmedUrls = [];
            foreach ($newUrls as $url) {
                $trimmedUrls[] = $this->trimGET($url);
            }

            $newUrls = array_unique($trimmedUrls);
        }

        $nonSimilarUrls = [];
        foreach ($newUrls as $url) {
            if ($this->similarUrlCheck($url))
            {
                $nonSimilarUrls[] = $url;
            }
        }

        return $nonSimilarUrls;
    }

    /**
     * Trim GET parameters.
     *
     * @param $url
     *
     * @return string
     */
    private function trimGET($url)
    {
        return strtok($url, '?');
    }

    /**
     * Create new crawl link.
     *
     * @param string $url
     *
     * @return CrawlLink
     */
    public function createCrawlLink($url)
    {
        $crawlLink = new CrawlLink();

        return $crawlLink->setLink($url)->setWebsite($this->website);
    }

    /**
     * Get number of priority crawl_links.
     *
     * @param Website $website
     * @param int     $minAvg
     *
     * @return array []
     */
    public function getPriorityCrawlLinks(Website $website, int $minAvg)
    {
        $crawlLinks = $this->crawlLinkRepo->getEfficientUrls($website, $minAvg);

//        todo: make smarter.

        return $crawlLinks;
    }

    /**
     * Get number of priority crawl_links.
     *
     * @param bool $rawLinks
     *
     * @return CrawlLink[]|array
     */
    public function getUncrawledLinks(bool $rawLinks = false): array
    {
        $crawlLinks = $this->crawlLinkRepo->getUnCrawledLinks($this->website);
        if ($rawLinks) {
            $crawlLinks = $this->getUrlsFromCrawlLinks($crawlLinks);
        }

        return $crawlLinks;
    }

    /**
     * Removes already crawled pages from database and adds the homepage as crawllink.
     *
     * @return void
     */
    public function resetCrawlData(): void
    {
        if (!isset($this->website)) {
            throw new CrawlException('Website not set.');
        }

        $this->deleteCrawlLinks($this->website);
        $initialCrawlLink = $this->website->getHomPageCrawlLink();
        $this->persistCrawlLink($initialCrawlLink);
        $this->flushCrawlLink($initialCrawlLink);
    }

    /**
     * Delete all websites crawlLinks
     *
     * @param Website $website
     */
    protected function deleteCrawlLinks(Website $website)
    {
        $crawlLinks = $website->getCrawlLinks();
        foreach ($crawlLinks as $crawlLink)
        {
            $this->entityManager->remove($crawlLink);
        }

        $this->entityManager->flush();
    }

    /**
     * Get array of urls from array of CrawlLinks.
     *
     * @param array|CrawlLink[] $crawlLinks
     *
     * @return array
     */
    public function getUrlsFromCrawlLinks(array $crawlLinks): array
    {
        $result = [];
        foreach ($crawlLinks as $crawlLink) {
            $result[] = $crawlLink->getLink();
        }

        return $result;
    }

    /**
     * Get Levenstein distance for url and decide if crawler is hopping autogenerated urls.
     *
     * @param string $url
     *
     * @return int
     */
    private function similarUrlCheck(string $url)
    {
        $distance = levenshtein($url, $this->lastUrl);

        $this->lastUrl = $url;

        if (2 >= $distance && $this->lowDistanceUrls >= 3) {
            return true;
        } elseif (2 >= $distance) {
            $this->lowDistanceUrls++;
        } else {
            $this->lowDistanceUrls = 0;
        }

        return false;
    }

    /**
     * Check if url is already in one of the links.
     *
     * @param CrawlLink[] $links
     * @param string      $url
     *
     * @return bool
     */
    public function checkUrlNew(
        $links,
        $url
    ) {
        foreach ($links as $link) {
            if ($link->getLink() === $url) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if URL is valid.
     *
     * @param $url
     *
     * @return bool
     */
    public function checkUrl(string $url)
    {
        if (null !== $this->config) {
            $ignoreList = $this->config['ignorePageUrlsWith'];
            foreach ($ignoreList as $ignoreString) {
                if (false !== strpos($url, $ignoreString)) {
                    return false;
                }
            }
        }

        if (
            strlen($url) > 2 &&    //Check if link is not empty of '/'
            '/' === $url[0]        //Check link is relative.
        ) {
            return true;
        }

        return false;
    }

    /**
     * Save array of links as crawl links.
     *
     * @param array $urls
     *
     * @return void
     */
    public function saveNewLinks(array $urls): void
    {
        foreach ($urls as $url) {
            $link = $this->createCrawlLink($url);
            $this->PersistCrawlLink($link);
            $this->flushCrawlLink($link);

            $this->website->addCrawlLink($link);
        }

        $this->PersistWebsite($this->website);
        $this->flushAll();
    }

    /**
     * Persist Crawl link.
     *
     * @param CrawlLink $crawlLink
     */
    public function persistCrawlLink(
        CrawlLink $crawlLink
    ) {
        $this->entityManager->persist($crawlLink);
    }

    /**
     * Flush crawl link.
     *
     * @param CrawlLink $crawlLink
     */
    public function flushCrawlLink(
        CrawlLink $crawlLink
    ) {
        $this->entityManager->flush($crawlLink);
    }

    /**
     * Persist website
     *
     * @param Website $website
     */
    public function persistWebsite(
        Website $website
    ) {
        $this->entityManager->persist($website);
    }

    /**
     * Flush website.
     *
     * @param Website $website
     */
    public function flushWebsite(
        Website $website
    ) {
        $this->entityManager->flush($website);
    }

    /**
     * Flush all.
     */
    public function flushAll()
    {
        $this->entityManager->flush();
    }
}
