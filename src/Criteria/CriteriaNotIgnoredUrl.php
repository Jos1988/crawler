<?php

namespace App\Criteria;

use App\Exceptions\FilterException;
use Psr\Http\Message\UriInterface;

class CriteriaNotIgnoredUrl extends LoggableCriteria
{
    /**
     * @var array
     */
    protected $config;

    /**
     * CriteriaNotIgnoredUrl constructor.
     *
     * @param array|null $config
     */
    public function __construct(array $config = null)
    {
        $this->config = $config;
    }

    /**
     * @param UriInterface $uri
     *
     * @return bool
     */
    public function meetCriteria(UriInterface $uri): bool
    {
        if (false === isset($this->config)) {
            throw new FilterException('no configuration set.');
        }

        $ignoreList = $this->config['ignorePageUrlsWith'];
        foreach ($ignoreList as $ignoreString) {
            if (false !== strpos($uri->getPath(), $ignoreString)) {
                $this->logger->alert(
                    sprintf(
                        'Url rejected, configured to ignore, url: %s, ignore: %s',
                        $uri->getPath(),
                        $ignoreString
                    )
                );

                return false;
            }
        }

        return true;
    }
}