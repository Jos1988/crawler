<?php

namespace App\UrlMapper;

use App\Config\WebsiteConfiguration;
use App\Criteria\AndCriteria;
use App\Criteria\CriteriaNew;
use App\Criteria\CriteriaNotIgnoredUrl;
use App\Criteria\CriteriaValidUrl;
use App\Entity\CrawlLink;
use App\Entity\Website;
use App\Repository\CrawlLinkRepository;
use App\Spider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Processor;

class UrlMapper implements UrlMapperInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var CrawlLinkRepository
     */
    protected $crawlLinkRepo;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var
     */
    protected $config;

    /**
     * @var string
     */
    private $lastUrl = '';

    /**
     * @var int
     */
    private $lowDistanceUrls;

    /**
     * UrlMapper constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->crawlLinkRepo = $em->getRepository(CrawlLink::class);
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $processor = new Processor();
        $configurator = new WebsiteConfiguration();

        $this->config = $processor->processConfiguration($configurator, $config);

    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Log message at specified level.
     *
     * @param $level
     * @param $message
     */
    protected function log($level, $message): void
    {
        if (isset($this->logger)) {
            $this->logger->log($level, $message);
        }
    }

    /**
     * @param Website $website
     *
     * @return array
     */
    protected function getWebsiteConfig(Website $website): array
    {
        foreach ($this->config['websites'] as $websiteConfigInstance) {
            if ($website->getName() === $websiteConfigInstance['name']) {
                return $websiteConfigInstance;
            }
        }
    }

    /**
     * Gets urls from website pages and updates website until no more new urls are discovered.
     *
     * @param Website         $website
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function mapWebsiteUrls(Website $website, LoggerInterface $logger = null): void
    {
        $linksToCrawl = $this->crawlLinkRepo->getUnCrawledLinks($website);

        if (empty($linksToCrawl)) {
            $homePage = new CrawlLink();
            $homePage->setWebsite($website)
                ->setLink('');

            $linksToCrawl[] = $homePage;
        }

        $this->log('notice', 'Starting Crawl.');

        do {
            $linkToCrawl = array_shift($linksToCrawl);
            $newCrawlLinks = $this->getUrlsFromPage($linkToCrawl);
            foreach ($newCrawlLinks as $crawlLink) {
                $website->addCrawlLink($crawlLink);
                $this->em->persist($crawlLink);
            }

            $this->em->persist($linkToCrawl);
            $this->em->persist($website);
            $this->em->flush();

            if (0 === count($linksToCrawl)) {
                $linksToCrawl = $this->crawlLinkRepo->getUnCrawledLinks($website);
                $this->log('notice', sprintf('%d links to crawl.', count($linksToCrawl)));
            }
        } while (0 !== count($linksToCrawl));

        $this->log('notice', 'Done.');
    }

    /**
     * @param CrawlLink $crawlLink
     *
     * @return CrawlLink[]
     */
    public function getUrlsFromPage(CrawlLink $crawlLink): array
    {
        $this->log('notice', 'crawling: '.$crawlLink->getLink());
        $website = $crawlLink->getWebsite();
        $config = $this->getWebsiteConfig($website);

        //crawl for links.
        $spider = new Spider($crawlLink);
        $spider->crawl();
        $newCrawlLinks = $spider->getUniqueCrawlLinks();

        //Set link as crawled.
        $crawlLink
            ->setCrawled(true)
            ->setCrawlDate(new \DateTime('now'));

        //trim GET params //todo: refactor
        if (true === $config['trimGET']) {
            $trimmed = [];
            foreach ($newCrawlLinks as $newCrawlLink) {
                $trimmedLink = strtok($newCrawlLink->getLink(), '?');
                $newCrawlLink->setLink($trimmedLink);
                $trimmed[] = $newCrawlLink;
            }

            $newCrawlLinks = $trimmed;
        }

        //todo trim # tags.
        $trimmed2 = [];
        foreach ($newCrawlLinks as $newCrawlLink) {
            $trimmedLink = strtok($newCrawlLink->getLink(), '#'); //todo: strtok???
            $newCrawlLink->setLink($trimmedLink);
            $trimmed2[] = $newCrawlLink;
        }

        $newCrawlLinks = $trimmed2;

        //filters
        $filter = new AndCriteria(
            new CriteriaValidUrl(),
            new CriteriaNotIgnoredUrl($config),
            new CriteriaNew($website->getCrawlLinks()->toArray())
        );

        /** @var CrawlLink[] $newCrawlLinks */
        $newCrawlLinks = $filter->meetCriteria($newCrawlLinks);

        //check similar urls. //todo: refactor
        $nonSimilarUrls = [];
        foreach ($newCrawlLinks as $newCrawlLink) {
            if (false === $this->similarUrlCheck($newCrawlLink->getLink())) {
                $nonSimilarUrls[] = $newCrawlLink;
            }
        }

        $newCrawlLinks = $nonSimilarUrls;

        if (0 !== count($newCrawlLinks)) {
            $this->log('info', (sprintf('Found %d new links.', count($newCrawlLinks))));
        }

        return $newCrawlLinks;
    }

    /**
     * Get Levenstein distance for url and decide if crawler is hopping autogenerated urls.
     *
     * @param string $url
     *
     * @return int
     */
    protected function similarUrlCheck(string $url)
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
}