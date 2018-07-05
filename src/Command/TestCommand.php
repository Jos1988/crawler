<?php

namespace App\Command;

use App\Crawler\MyCrawlObserver;
use Psr\Log\LoggerInterface;
use Spatie\Crawler\Crawler;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand
{
    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * TestCommand constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct();
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this->setName('test:test')
            ->setDescription('test');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = 'https://www.heturkerland.nl/';
        $observer = new MyCrawlObserver($this->logger);

        Crawler::create()
            ->setMaximumCrawlCount(10)
            ->setCrawlObserver($observer)
            ->startCrawling($url);
    }
}