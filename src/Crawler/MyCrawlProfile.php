<?php

namespace App\Crawler;

use App\Criteria\AndCriteria;
use App\Criteria\CriteriaInterface;
use App\Criteria\CriteriaInternal;
use App\Criteria\CriteriaInternalUrl;
use App\Criteria\CriteriaLongEnough;
use App\Criteria\CriteriaNew;
use App\Criteria\CriteriaNoImage;
use App\Criteria\CriteriaNonSimilar;
use App\Criteria\CriteriaNotIgnoredUrl;
use App\Entity\CrawlLink;
use App\Entity\Website;
use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerInterface;
use Spatie\Crawler\CrawlProfile;

class MyCrawlProfile extends CrawlProfile
{
    /**
     * @var Website
     */
    protected $website;

    /**
     * @var array
     */
    protected $websiteConfig;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CriteriaInterface
     */
    protected $filter;

    /**
     * MyCrawlProfile constructor.
     *
     * @param Website         $website
     * @param LoggerInterface $logger
     * @param array           $websiteConfig
     */
    public function __construct(Website $website, LoggerInterface $logger, array $websiteConfig)
    {
        $this->website = $website;
        $this->logger = $logger;
        $this->websiteConfig = $websiteConfig;
        $this->setFilter();
    }

    /**
     * Set filter
     */
    protected function setFilter()
    {
        $this->filter = new AndCriteria(
            new CriteriaNotIgnoredUrl($this->websiteConfig),
            new CriteriaNonSimilar(),
            new CriteriaInternal($this->website->getUrl())
        );

        $this->filter->setLogger($this->logger);
    }

    /**
     * @param UriInterface $url
     *
     * @return bool
     */
    public function shouldCrawl(UriInterface $url): bool
    {
        if ($this->filter->meetCriteria($url)) {
            return true;
        }

        return false;
    }
}