<?php

namespace App\Criteria;

use App\Entity\CrawlLink;
use Psr\Log\LoggerInterface;

/**
 * filters urls if they are too similar to each other.
 *
 * Class ValidUrlCriteria
 * @package App\CrawlLinkCriteria
 */
class CriteriaNonSimilar implements Criteria
{
    /** @var string */
    protected $lastUrl = '';

    /** @var int */
    protected $similarUrls = 0;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * CriteriaNonSimilar constructor.
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

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
            if (false === $this->similarUrlCheck($crawlLink->getLink())) {
                $valid[] = $crawlLink;
            }
        }

        return $valid;
    }

    /**
     * Get Levenstein distance for url and decide if crawler is hopping autogenerated urls.
     *
     * @param string $url
     *
     * @return int
     */
    protected function similarUrlCheck(string $url)
    {
        $distance = levenshtein($url, $this->lastUrl);

        $this->lastUrl = $url;

        if (2 >= $distance && $this->similarUrls >= 3) {
            return true;
        } elseif (2 >= $distance) {
            $this->similarUrls++;
            $this->logger->alert(
                sprintf("Url's to similar. current: %S, previous: %S  ", $url, $this->lastUrl)
            );

        } else {
            $this->similarUrls = 0;
        }

        return false;
    }
}