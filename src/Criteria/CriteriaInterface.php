<?php

namespace App\Criteria;

use Psr\Http\Message\UriInterface;

interface CriteriaInterface
{
    /**
     * @param UriInterface $crawlLinks
     *
     * @return bool
     */
    public function meetCriteria(UriInterface $crawlLinks): bool;
}