<?php

namespace App\Criteria;

use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

abstract class LoggableCriteria implements CriteriaInterface, LoggerAwareInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     *
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param UriInterface $uri
     *
     * @return bool
     */
    abstract function meetCriteria(UriInterface $uri): bool;
}