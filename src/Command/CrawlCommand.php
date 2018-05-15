<?php

namespace App\Command;

use App\Config\CrawlerConfiguration;
use App\CrawlLinkCriteria\AndCriteria;
use App\CrawlLinkCriteria\CriteriaNew;
use App\CrawlLinkCriteria\CriteriaNotIgnoredUrl;
use App\CrawlLinkCriteria\CriteriaValidUrl;
use App\Entity\Website;
use App\Service\Spider;
use App\Service\SpiderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

class CrawlCommand extends ContainerAwareCommand
{
    /**
     * @var SpiderService
     */
    private $spiderService;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * SpiderCommand constructor.
     *
     * @param null|string            $name
     * @param EntityManagerInterface $em
     */
    public function __construct(?string $name = null, EntityManagerInterface $em)
    {
        parent::__construct($name);

        $this->em = $em;
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
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $websiteRepo = $this->em->getRepository(Website::class);
        $websites = $websiteRepo->findAll();
        $writer = new SymfonyStyle($input, $output);
        $writer->newLine();

        //write out crawlable websites.
        foreach ($websites as $website) {
            $writer->comment($website->getId().'| '.$website->getName());
        }

        $websiteId = $writer->ask('Please enter website id:');
        $answer = $writer->confirm('Send spiders???', false);
        if (false === $answer) {
            $writer->caution('crawler terminated.');

            return false;
        }

        $website = $websiteRepo->findOneBy(['id' => $websiteId]);
        $config = $this->validateConfig();

        /** @var Website $website */
        $websiteConfig = null;
        foreach ($config['websites'] as $websiteConfigInstance) {
            if ($website->getName() === $websiteConfigInstance['name']) {
                $websiteConfig = $websiteConfigInstance;
            }
        }

        $this->spiderService = new SpiderService($this->em, $websiteConfig);
        $this->spiderService->setWebsite($website);

        //initial crawl, clear all crawl links and add homepage
        $this->spiderService->resetCrawlData();

        $linksToCrawl = $this->spiderService->getUncrawledLinks(false);

        $writer->block('Starting Crawl.');

        do {
            $crawlLink = array_shift($linksToCrawl);
            $writer->comment('crawling: '.$crawlLink->getLink());

            //crawl for links.
            $spider = new Spider($crawlLink);
            $newCrawlLinks = $spider->getUniqueCrawlLinks();

            //Set link as crawled.
            $crawlLink
                ->setCrawled(true)
                ->setCrawlDate(new \DateTime('now'));

            $this->em->persist($crawlLink);
            $this->em->flush();

            //filter
            $filter = new AndCriteria(
                new CriteriaValidUrl(),
                new CriteriaNotIgnoredUrl($websiteConfig),
                new CriteriaNew($website->getCrawlLinks())
            );

            //trim GET params

            //check similar urls.

            $newCrawlLinks = $filter->meetCriteria($newCrawlLinks);

            $this->spiderService->saveNewLinks($newCrawlLinks);

            $writer->comment(sprintf('Persisting %d new links.', count($newCrawlLinks)));

            if (0 === count($linksToCrawl)) {
                $linksToCrawl = $this->spiderService->getUncrawledLinks(false);
                $writer->comment(sprintf('%d links to crawl.', count($linksToCrawl)));
            }
        } while (0 !== count($linksToCrawl));
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
        $configurator = new CrawlerConfiguration();

        return $processor->processConfiguration($configurator, $config);
    }
}