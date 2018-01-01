<?php

namespace App\Command;

use App\Entity\Website;
use App\Service\SpiderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\VarDumper\Cloner\VarCloner;

class SpiderCommand extends ContainerAwareCommand
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
//        $container = $this->getContainer();
//        $spiderService = $container->get(SpiderService::class);
//         = $container->get(WebsiteRepository::class);
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
            $newLinks += $this->spiderService->crawlSite($website, $writer);
        }

        $writer->success('Added '. $newLinks .' new crawl links');
    }
}