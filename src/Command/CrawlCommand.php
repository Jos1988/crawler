<?php

namespace App\Command;

use App\Entity\CrawlLink;
use App\Entity\Website;
use App\Repository\CrawlLinkRepository;
use App\UrlMapper\UrlMapper;
use App\UrlMapper\UrlMapperInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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
     * @throws \Doctrine\ORM\NonUniqueResultException
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

        $this->crawlLinkRepo->setLinksNotCrawled($website);
        $numberOfUnCrawledLinks = $this->crawlLinkRepo->countUnCrawledLinks($website);

        $this->mapper->setConfig($config);

        $writer->title(
            "Now mapping: '{$website->getName()}' with a total of {$numberOfUnCrawledLinks} un-crawled links."
        );

        $totalLinks = $this->mapper->mapWebsiteUrls($website);

        $writer->success("Done, found {$totalLinks} links in total.");
    }
}