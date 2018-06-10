<?php

namespace App\Criteria;

use App\Entity\CrawlLink;
use App\Exceptions\FilterException;
use RobotsTxtParser;

class CriteriaIsRobotAllowed implements Criteria
{
    /**
     * @var RobotsTxtParser
     */
    protected $robotsTxtParser;

    /**
     * CriteriaIsRobotAllowed constructor.
     *
     * @param RobotsTxtParser $robotsTxtParser
     */
    public function __construct(RobotsTxtParser $robotsTxtParser)
    {
        $this->robotsTxtParser = $robotsTxtParser;
    }

    /**
     * @param array $crawlLinks
     *
     * @return array
     */
    public function meetCriteria(array $crawlLinks): array
    {
        if (false === isset($this->robotsTxtParser)) {
            throw new FilterException('no RobotsTxtParser set.');
        }

        $allowed = [];
        /** @var CrawlLink $crawlLink */
        foreach ($crawlLinks as $crawlLink) {
            $url = $crawlLink->getLink();
            if ($this->robotsTxtParser->isAllowed($url, getenv('SPIDER_UA'))) {
                $allowed[] = $crawlLink;
            }
        }

        return $allowed;
    }
}