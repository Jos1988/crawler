<?php

namespace App\Criteria;

use App\Entity\CrawlLink;

interface Criteria
{
    /**
     * @param array $crawlLinks
     *
     * @return array|CrawlLink[]
     */
    public function meetCriteria(array $crawlLinks): array;
}