<?php

namespace App\Command;

use App\Config\CrawlerConfiguration;
use App\Entity\Website;
use App\Service\SpiderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

class ReCrawlCommand extends ContainerAwareCommand
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
     * @param SpiderService          $spiderService
     * @param EntityManagerInterface $em
     */
    public function __construct(?string $name = null, SpiderService $spiderService, EntityManagerInterface $em)
    {
        parent::__construct($name);

        $this->spiderService = $spiderService;
        $this->em = $em;
    }

    protected function configure()
    {
        $this->setName('newssearch:spider:recrawl')
            ->setDescription('Send spider to re-crawl targets, faster and less thorough than than crawl.');
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
        $config = $this->validateConfig();

        $newLinks = 0;
        $websiteRepo = $this->em->getRepository(Website::class);

        $writer = new SymfonyStyle($input, $output);
        $answer = $writer->confirm('Send spiders???', false);

        if (false === $answer) {
            $writer->caution('crawler terminated.');

            return false;
        }

        $websites = $websiteRepo->findAll();
        /** @var Website $website */
        foreach ($websites as $website) {

            $websiteConfig = null;
            foreach ($config['websites'] as $websiteConfigInstance) {
                if ($website->getName() === $websiteConfigInstance['name']) {
                    $websiteConfig = $websiteConfigInstance;
                }
            }

            $newLinks += $this->spiderService->CrawlSite($website, $writer, $websiteConfig, true);
        }

        $writer->success('Added '.$newLinks.' new crawl links');
    }

    /**
     * validate crawler configuration.
     *
     * @return array
     */
    private function validateConfig()
    {
        $config = Yaml::parse(
            file_get_contents(__DIR__.'/../../config/crawler.yaml')
        );

        $processor = new Processor();
        $configurator = new CrawlerConfiguration();

        return $processor->processConfiguration($configurator, $config);
    }
}