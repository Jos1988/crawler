<?php

namespace App\Criteria;

use App\Exceptions\FilterException;
use Psr\Http\Message\UriInterface;

class CriteriaInternal extends LoggableCriteria
{
    /**
     * @var string
     */
    protected $domain;

    /**
     * CriteriaNotIgnoredUrl constructor.
     *
     * @param string $domain
     */
    public function __construct(string $domain)
    {
        $this->domain = $domain;
    }

    /**
     * @param UriInterface $uri
     *
     * @return bool
     */
    public function meetCriteria(UriInterface $uri): bool
    {
        if (false === isset($this->domain)) {
            throw new FilterException('Website domain not set on Internal filter.');
        }

        if (false == strpos($this->domain, $uri->getHost())) {
            return false;
        }

        return true;
    }
}