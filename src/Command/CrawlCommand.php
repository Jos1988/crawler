<?php

namespace App\Command;

use App\Config\WebsiteConfiguration;
use App\Entity\CrawlLink;
use App\Entity\Website;
use App\Repository\CrawlLinkRepository;
use App\UrlMapper\UrlMapper;
use App\UrlMapper\UrlMapperInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

class CrawlCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UrlMapperInterface
     */
    private $mapper;

    /**
     * @var CrawlLinkRepository
     */
    private $crawlLinkRepo;

    /**
     * @var EntityRepository
     */
    private $websiteRepo;

    /**
     * @var string
     */
    private $lastUrl = '';

    /**
     * @var int
     */
    private $lowDistanceUrls;

    /**
     * SpiderCommand constructor.
     *
     * @param null|string              $name
     * @param EntityManagerInterface   $em
     * @param UrlMapper                $urlMapper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        ?string $name = null,
        EntityManagerInterface $em,
        UrlMapper $urlMapper,
        LoggerInterface $logger
    ) {
        parent::__construct($name);

        $this->em = $em;
        $this->mapper = $urlMapper;
        $this->mapper->setLogger($logger);
        $this->crawlLinkRepo = $this->em->getRepository(CrawlLink::class);
        $this->websiteRepo = $this->em->getRepository(Website::class);
    }

    protected function configure()
    {
        $this->setName('newssearch:spider:crawl')
            ->setDescription('Send spider to crawl targets');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $websites = $this->websiteRepo->findAll();
        $writer = new SymfonyStyle($input, $output);
        $writer->newLine();

        //write out websites.
        foreach ($websites as $website) {
            $writer->comment($website->getId().'| '.$website->getName());
        }

        $websiteId = $writer->ask('Please enter website id:');
        $answer = $writer->confirm('Send spiders???', false);
        if (false === $answer) {
            $writer->caution('crawler terminated.');

            return false;
        }

        $website = $this->websiteRepo->findOneBy(['id' => $websiteId]);

        $config = Yaml::parse(
            file_get_contents(__DIR__.'/../../config/crawler.yaml')
        );

        $this->mapper->setConfig($config);
        $this->mapper->mapWebsiteUrls($website);

//        //initial crawl, clear all crawl links and add homepage.
//        $website->clearCrawlLinks();
//        $homepage = $website->getHomPageCrawlLink();
//        $this->em->persist($website);
//        $this->em->flush();
//
//        $this->em->persist($homepage);
//        $this->em->flush();
//
//        $linksToCrawl = $this->crawlLinkRepo->getUnCrawledLinks($website);
//
//        $writer->block('Starting Crawl.');

//        do {
//            $crawlLink = array_shift($linksToCrawl);
//
//            $writer->comment('crawling: '.$crawlLink->getLink());
//
//            //crawl for links.
//            $spider = new Spider($crawlLink);
//            $spider->crawl();
//            $newCrawlLinks = $spider->getUniqueCrawlLinks();
//
//            //Set link as crawled.
//            $crawlLink
//                ->setCrawled(true)
//                ->setCrawlDate(new \DateTime('now'));
//
//            $this->em->persist($crawlLink);
//            $this->em->flush();
//
//            //trim GET params //todo: refactor to transformater??
//            if (true === $websiteConfig['trimGET']) {
//                $trimmed = [];
//                foreach ($newCrawlLinks as $newCrawlLink) {
//                    $trimmedLink = strtok($newCrawlLink->getLink(), '?');
//                    $newCrawlLink->setLink($trimmedLink);
//                    $trimmed[] = $newCrawlLink;
//                }
//
//                $newCrawlLinks = $trimmed;
//            }
//
//            //filters
//            $filter = new AndCriteria(
//                new CriteriaValidUrl(),
//                new CriteriaNotIgnoredUrl($websiteConfig),
//                new CriteriaNew($website->getCrawlLinks()->toArray())
//            );
//
//            /** @var CrawlLink[] $newCrawlLinks */
//            $newCrawlLinks = $filter->meetCriteria($newCrawlLinks);
//
//            //check similar urls. //todo: refactor
//            $nonSimilarUrls = [];
//            foreach ($newCrawlLinks as $newCrawlLink) {
//                if (false === $this->similarUrlCheck($newCrawlLink->getLink())) {
//                    $nonSimilarUrls[] = $newCrawlLink;
//                }
//            }
//
//            $newCrawlLinks = $nonSimilarUrls;
//
//            $writer->comment(sprintf('Persisting %d new links.', count($newCrawlLinks)));
//            foreach ($newCrawlLinks as $newCrawlLink) {
//                $this->em->persist($newCrawlLink);
//                $website->addCrawlLink($newCrawlLink);
//            }
//
//            $this->em->persist($crawlLink);
//            $this->em->flush();
//
//            if (0 === count($linksToCrawl)) {
//                $linksToCrawl = $this->crawlLinkRepo->getUnCrawledLinks($website);
//                $writer->comment(sprintf('%d links to crawl.', count($linksToCrawl)));
//            }
//        } while (0 !== count($linksToCrawl));
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
     * validate crawler configuration.
     *
     * @return array
     */
    private function validateConfig(): array
    {
        $config = Yaml::parse(
            file_get_contents(__DIR__.'/../../config/crawler.yaml')
        );

        $processor = new Processor();
        $configurator = new WebsiteConfiguration();

        return $processor->processConfiguration($configurator, $config);
    }
}