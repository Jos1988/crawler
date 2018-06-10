<?php

namespace App\Criteria;

use App\Entity\CrawlLink;

/**
 * Class ValidUrlCriteria
 * @package App\CrawlLinkCriteria
 */
class CriteriaLongEnough implements Criteria
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
            if (strlen($url) > 2) {
                $valid[] = $crawlLink;
            }
        }

        return $valid;
    }
}