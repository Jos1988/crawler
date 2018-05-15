<?php

namespace App\CrawlLinkCriteria;

use App\Entity\CrawlLink;

/**
 * Class ValidUrlCriteria
 * @package App\CrawlLinkCriteria
 */
class CriteriaNew implements Criteria
{
    /**
     * @var CrawlLink[]
     */
    private $existingCrawlLinks;

    /**
     * CriteriaNew constructor.
     *
     * @param array $existingCrawlLinks
     */
    public function __construct(array $existingCrawlLinks)
    {
        $this->existingCrawlLinks = $existingCrawlLinks;
    }

    /**
     * @param CrawlLink[] $crawlLinks
     *
     * @return array
     */
    public function meetCriteria(array $crawlLinks): array
    {
        $valid = [];

        foreach ($crawlLinks as $crawlLink) {
            $unique = true;
            foreach ($this->existingCrawlLinks as $existingCrawlLink) {
                if ($crawlLink->getLink() === $existingCrawlLink->getLink()) {
                    $unique = false;
                    break;
                }
            }

            if ($unique) {
                $valid[] = $crawlLink;
            }
        }

        return $valid;
    }
}