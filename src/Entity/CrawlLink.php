<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CrawlLink
 *
 * @ORM\Table(name="crawl_link")
 * @ORM\Entity(repositoryClass="App\Repository\CrawlLinkRepository")
 */
class CrawlLink
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255, unique=true)
     */
    private $link;

    /**
     * @var bool
     *
     * @ORM\Column(name="crawled", type="boolean", options={"default"=false})
     */
    private $crawled = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="crawlDate", type="datetime", nullable=true)
     */
    private $crawlDate;

    /**
     * @var Website
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Website", inversedBy="crawlLinks")
     */
    private $website;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set link
     *
     * @param string $link
     *
     * @return CrawlLink
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Get absolute url.
     *
     * @return string
     */
    public function getAbsoluteLink()
    {
        return ltrim($this->website->getDomain(), '/') . $this->getLink();
    }

    /**
     * Set crawled
     *
     * @param boolean $crawled
     *
     * @return CrawlLink
     */
    public function setCrawled($crawled)
    {
        $this->crawled = $crawled;

        return $this;
    }

    /**
     * Get crawled
     *
     * @return bool
     */
    public function isCrawled()
    {
        return $this->crawled;
    }

    /**
     * Set crawlDate
     *
     * @param \DateTime $crawlDate
     *
     * @return CrawlLink
     */
    public function setCrawlDate($crawlDate)
    {
        $this->crawlDate = $crawlDate;

        return $this;
    }

    /**
     * Get crawlDate
     *
     * @return \DateTime
     */
    public function getCrawlDate()
    {
        return $this->crawlDate;
    }

    /**
     * get Website
     *
     * @return Website
     */
    public function getWebsite(): Website
    {
        return $this->website;
    }

    /**
     * set Website
     *
     * @param Website $website
     *
     * @return CrawlLink
     */
    public function setWebsite(Website $website)
    {
        $this->website = $website;

        return $this;
    }
}

