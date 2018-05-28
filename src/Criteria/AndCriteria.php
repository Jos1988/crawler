<?php
/**
 * Created by PhpStorm.
 * User: Jos
 * Date: 13-5-2018
 * Time: 12:00
 */

namespace App\Criteria;

/**
 * Class ValidUrlCriteria
 * @package App\CrawlLinkCriteria
 */
class AndCriteria implements Criteria
{
    /**
     * @var Criteria[]
     */
    private $criteria = [];

    public function __construct(Criteria ...$criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * @param array $crawlLinks
     *
     * @return array
     */
    public function meetCriteria(array $crawlLinks): array
    {
        foreach ($this->criteria as $criterion) {
            $crawlLinks = $criterion->meetCriteria($crawlLinks);
        }

        return $crawlLinks;
    }
}