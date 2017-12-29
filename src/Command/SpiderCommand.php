<?php

namespace App\Command;

use App\Entity\Website;
use App\Repository\WebsiteRepository;
use App\Service\SpiderService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SpiderCommand extends ContainerAwareCommand
{
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
        $newLinks = 0;
        $container = $this->getContainer();
        $spiderService = $container->get(SpiderService::class);
        $websiteRepo = $container->get(WebsiteRepository::class);

        $writer = new SymfonyStyle($input, $output);
        $answer = $writer->confirm('Send spiders???', false);

        if (false === $answer) {
            $writer->caution('crawler terminated.');

            return false;
        }

        $websites = $websiteRepo->findAll();
        /** @var Website $website */
        foreach ($websites as $website) {
            $newLinks += $spiderService->crawlSite($website, $writer);
        }

        $writer->success('Added '. $newLinks .' new crawl links');
    }
}