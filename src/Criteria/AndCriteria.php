<?php

namespace App\Criteria;

use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ValidUrlCriteria
 * @package App\CrawlLinkCriteria
 */
class AndCriteria extends LoggableCriteria
{
    /**
     * @var CriteriaInterface[]
     */
    private $criteria = [];

    public function __construct(CriteriaInterface ...$criteria)
    {
        $this->criteria = $criteria;
    }

    public function setLogger(LoggerInterface $logger)
    {
        parent::setLogger($logger);

        foreach ($this->criteria as $criteria)
        {
            if ($criteria instanceof LoggerAwareInterface) {
                $criteria->setLogger($this->logger);
            }
        }
    }

    /**
     *
     * @param UriInterface $uri
     *
     * @return bool
     */
    public function meetCriteria(UriInterface $uri): bool
    {
        foreach ($this->criteria as $criterion) {
            if (false === $criterion->meetCriteria($uri)) {
                return false;
            }
        }

        return true;
    }
}