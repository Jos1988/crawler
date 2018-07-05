<?php

namespace App\UrlMapper;

use App\Config\WebsiteConfiguration;
use App\Crawler\MyCrawlObserver;
use App\Crawler\MyCrawlProfile;
use App\Entity\CrawlLink;
use App\Entity\Website;
use App\Repository\CrawlLinkRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use RobotsTxtParser;
use Spatie\Crawler\Crawler;
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
     * @var RobotsTxtParser
     */
    protected $robotsTxtParser;

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
     * @param Website $website
     *
     * @return int
     */
    public function mapWebsiteUrls(Website $website): int
    {
        $this->log('notice', 'Starting Crawl.');

        $websiteConfig = $this->getWebsiteConfig($website);
        $profile = new MyCrawlProfile($website, $this->logger, $websiteConfig);
        $observer = new MyCrawlObserver($this->logger, $this->em, $website);

        Crawler::create()
            ->setMaximumCrawlCount(200)
            ->setConcurrency(1)
            ->setCrawlProfile($profile)
            ->setCrawlObserver($observer)
            ->startCrawling($website->getUrl());

        $observer->flushBatch($observer->getBatch());

        $totalNewLinks = $observer->getCrawlCount();

        return $totalNewLinks;
    }
}