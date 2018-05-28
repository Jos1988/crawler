<?php
/**
 * Created by PhpStorm.
 * User: Jos
 * Date: 13-5-2018
 * Time: 12:00
 */

namespace App\CrawlLinkCriteria;

use App\Entity\CrawlLink;

/**
 * Class ValidUrlCriteria
 * @package App\CrawlLinkCriteria
 */
class CriteriaValidUrl implements Criteria
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
            if (strlen($url) > 2 && '/' === $url[0]) {
                $valid[] = $crawlLink;
            }
        }

        return $valid;
    }
}