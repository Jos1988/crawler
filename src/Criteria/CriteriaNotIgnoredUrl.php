<?php

namespace App\Criteria;

use App\Entity\CrawlLink;
use App\Exceptions\FilterException;

class CriteriaNotIgnoredUrl implements Criteria
{
    /**
     * @var array
     */
    protected $config;

    public function __construct(array $config = null)
    {
        $this->config = $config;
    }

    public function meetCriteria(array $crawlLinks): array
    {
        if (false === isset($this->config)) {
            throw new FilterException('no configuration set.', 500);
        }

        $notIgnored = [];
        /** @var CrawlLink $crawlLink */
        foreach ($crawlLinks as $crawlLink) {
            $url = $crawlLink->getLink();
            $ignoreList = $this->config['ignorePageUrlsWith'];
            foreach ($ignoreList as $ignoreString) {
                if (false !== strpos($url, $ignoreString)) {
                    continue;
                }
            }

            $notIgnored[] = $crawlLink;
        }

        return $notIgnored;
    }
}