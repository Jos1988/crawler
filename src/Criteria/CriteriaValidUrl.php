<?php
/**
 * Created by PhpStorm.
 * User: Jos
 * Date: 13-5-2018
 * Time: 12:00
 */

namespace App\Criteria;

use App\Entity\CrawlLink;

/**
 * Class ValidUrlCriteria
 * @package App\CrawlLinkCriteria
 */
class CriteriaValidUrl implements Criteria
{ //todo: change name... valid is to vague.
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
            if (strlen($url) > 2 && '/' === $url[0]) { //todo refactor to two filters.
                $valid[] = $crawlLink;                 //todo relative url check should also account for absolute urls
            }                                          //todo that point to the same website.
        }

        return $valid;
    }
}