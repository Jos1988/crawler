<?php

namespace App\Criteria;

use App\Entity\CrawlLink;

/**
 * Class ValidUrlCriteria
 * @package App\CrawlLinkCriteria
 */
class CriteriaInternalUrl implements Criteria
{
    /**
     * @param array $crawlLinks
     *
     * @return array
     */
    public function meetCriteria(array $crawlLinks): array
    {
        $valid = [];
        /** @var CrawlLink $crawlLink */
        foreach ($crawlLinks as $crawlLink) {
            $url = $crawlLink->getLink();
            if ('/' === $url[0]) {
                $valid[] = $crawlLink;
            }
        }

        return $valid;
    }
}