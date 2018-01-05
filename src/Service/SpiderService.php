<?php

namespace App\Service;

use App\Entity\CrawlLink;
use App\Entity\Website;
use App\Repository\CrawlLinkRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;

class SpiderService
{
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

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->crawlLinkRepo = $this->entityManager->getRepository(CrawlLink::class);
    }

    /**
     * Create new crawl link.
     *
     * @param Website $website
     * @param string  $url
     *
     * @return CrawlLink
     */
    public function createCrawlLink(Website $website, $url)
    {
        $crawlLink = new CrawlLink();

        return $crawlLink->setLink($url)->setWebsite($website);
    }

    /**
     * crawl site.
     *
     * @param Website           $website
     * @param SymfonyStyle|null $writer
     * @param array|null        $config
     *
     * @return int
     */
    public function crawlSite(Website $website, SymfonyStyle $writer = null, array $config = null)
    {
        $spider = new Spider($website->getUrl());
        $spider->crawl();
        $urls = $spider->getUniqueLinks();
        $oldLinks = $website->getCrawlLinks();
        $links = [];

        $this->config = $config;

        if (null !== $writer) {
            $writer->success('starting Crawl');
        }

        $newUrl = 0;
        foreach ($urls as $url) {
            if ($this->checkUrl($url) && $this->checkUrlNew($oldLinks, $url)) {
                if (null !== $writer) {
                    $writer->comment('Adding new URL: '.$url);
                }

                $newUrl++;
                $link = $this->createCrawlLink($website, $url);
                $links[] = $link;
            }
        }

        $this->saveNewLinks($links, $website);

        return $newUrl + $this->deepCrawlLinks($spider, $website, $writer);
    }

    /**
     * Crawl array of links.
     *
     * @param Spider            $spider
     * @param Website           $website
     * @param SymfonyStyle|null $writer
     *
     * @return int
     */
    public function deepCrawlLinks(Spider $spider, Website $website, SymfonyStyle $writer = null)
    {
        $newLinkNr = 0;
        $run = true;
        $allLinks = $this->crawlLinkRepo->getAllUrls($website);

        //run until there are no more uncrawled links.
        while ($run) {
            $links = $this->crawlLinkRepo->getUnCrawledLinks($website);
            if (null !== $writer) {
                $writer->comment(
                    'Crawling '.count($links).' new links out of a total of '.count($allLinks).' links .'
                );
            }

            $run = false;
            $newLinks = [];
            //Check the current set of links.
            foreach ($links as $link) {
                //go through the link set again.
                $run = true;
                //Gather all known urls.
                $newLinks = array_merge($newLinks, $this->deepCrawl($link, $spider, $website, $allLinks, $writer));
                $allLinks = array_merge($this->getUrlsFromCrawlLinks($newLinks), $allLinks);
            }

            $this->saveNewLinks(array_merge($links, $newLinks), $website);
        }

        return $newLinkNr;
    }

    /**
     * Crawl link for more links.
     *
     * @param CrawlLink         $link
     * @param Spider            $spider
     * @param Website           $website
     * @param array             $allLinks
     * @param SymfonyStyle|null $writer
     *
     * @return array
     */
    public function deepCrawl(
        CrawlLink $link,
        Spider $spider,
        Website $website,
        array $allLinks,
        SymfonyStyle $writer = null
    ) {
        //Set link as crawled.
        $link->setCrawled(true)->setCrawlDate(new DateTime('now'));

        //Crawl the link
        $result = $spider->crawl($link->getAbsoluteLink());

        //if crawl succesful.
        if ($result instanceof Crawler) {
            $newLinks = [];

            //get resulting url's.
            $urls = $spider->getUniqueLinks();
            foreach ($urls as $url) {
                //If the're not in the current set of links.
                if ($this->checkUrl($url) && false === in_array($url, $allLinks)) {
                    //Add Url to set of links.
                    if (null !== $writer) {
                        $writer->comment('Adding new URL: '.$url);
                    }

                    $newLink = $this->createCrawlLink($website, $url);
                    $newLinks[] = $newLink;

                    //Save new url as known url.
                    $allLinks[] = $url;
                }
            }

            return $newLinks;
        }

        $writer->error('curl error crawling: '.$result);
    }

    /**
     * Get array of urls from array of CrawlLinks.
     *
     * @param array|CrawlLink[] $crawlLinks
     *
     * @return array
     */
    public function getUrlsFromCrawlLinks(array $crawlLinks)
    {
        $result = [];
        foreach ($crawlLinks as $crawlLink) {
            $result[] = $crawlLink->getLink();
        }

        return $result;
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
    public function checkUrl(
        $url
    ) {
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
     * @param CrawlLink[] $links
     * @param Website     $website
     *
     */
    public function saveNewLinks(
        array $links,
        Website $website
    ) {
        foreach ($links as $link) {
            $this->PersistCrawlLink($link);
            $this->flushCrawlLink($link);

            $website->addCrawlLink($link);
        }

        $this->PersistWebsite($website);
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
