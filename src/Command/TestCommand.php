<?php

namespace App\Command;

use App\Entity\CrawlLink;
use App\Modifiers\CrawlLinkModifiers\ModGetTrim;
use App\Modifiers\CrawlLinkModifiers\ModIDTrim;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand
{
    /**
     * TestCommand constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();
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
        $getTrimmer = new ModGetTrim();
        $idTrimmer = new ModIDTrim();

//        $idTrimmer->chain($getTrimmer);

        $input = new CrawlLink();
        $input->setLink('test1?test2#test3');

        $output1 = $getTrimmer->modify($input);
        dump($output1->getLink());

//        dump('--------');
//        $getTrimmer = new GetTrimmer();
//        $idTrimmer = new IdTrimmer();
////        $idTrimmer->chain($getTrimmer);
//        $input = new CrawlLink();
//        $input->setLink('test#test?test');
//
//        $output2 = $idTrimmer->modify($input);
//        dump($output2->getLink());
    }
}