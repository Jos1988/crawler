<?php

namespace App\Criteria;

use App\Entity\CrawlLink;

/**
 * Class ValidUrlCriteria
 * @package App\CrawlLinkCriteria
 */
class CriteriaNoImage implements Criteria
{
    static $imageExtensions = ['tif', 'tiff','gif','jpeg', 'jpg', 'jif', 'jfif','jp2' ,'jpx','j2k','j2c','fpx','pcd','png', 'pdf'];

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
            $explodedUrl = explode('.', $url);
            $extension = strtolower(end($explodedUrl));
            if (false === in_array($extension, self::$imageExtensions)) {
                $valid[] = $crawlLink;
            }
        }

        return $valid;
    }
}