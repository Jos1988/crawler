<<<<<<< HEAD
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use \DateTime;

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
     * @ORM\Column(name="crawl_date", type="datetime", nullable=true)
     */
    private $crawlDate;

    /**
     * Number of times crawled.
     *
     * @var int
     *
     * @ORM\Column(name="crawl_count", type="integer", nullable=false, options={"default"=0})
     */
    private $crawlCount = 0;

    /**
     * Number of new links found at this address.
     *
     * @var int
     *
     * @ORM\Column(name="crawl_successes", type="integer", nullable=false, options={"default"=0})
     */
    private $crawlSuccesses = 0;

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
    public function setLink(string $link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * Get absolute url.
     *
     * @return string
     */
    public function getAbsoluteLink(): string
    {
        return ltrim($this->website->getUrl(), '/') . $this->getLink();
    }

    /**
     * Set crawled
     *
     * @param boolean $crawled
     *
     * @return CrawlLink
     */
    public function setCrawled(bool $crawled)
    {
        $this->crawled = $crawled;

        return $this;
    }

    /**
     * Get crawled
     *
     * @return bool
     */
    public function isCrawled(): bool
    {
        return $this->crawled;
    }

    /**
     * Set crawlDate
     *
     * @param DateTime $crawlDate
     *
     * @return CrawlLink
     */
    public function setCrawlDate(DateTime $crawlDate)
    {
        $this->crawlDate = $crawlDate;

        return $this;
    }

    /**
     * Get crawlDate
     *
     * @return DateTime
     */
    public function getCrawlDate(): DateTime
    {
        return $this->crawlDate;
    }

    /**
     * get CrawlCount
     *
     * @return int
     */
    public function getCrawlCount(): int
    {
        return $this->crawlCount;
    }

    /**
     * set CrawlCount
     *
     * @param int $crawlCount
     *
     * @return CrawlLink
     */
    public function setCrawlCount(int $crawlCount)
    {
        $this->crawlCount = $crawlCount;

        return $this;
    }

    /**
     * add one to crawl count.
     *
     * @return CrawlLink
     */
    public function addCrawlCount()
    {
        $this->crawlCount++;

        return $this;
    }

    /**
     * get CrawlSuccesses
     *
     * @return int
     */
    public function getCrawlSuccesses(): int
    {
        return $this->crawlSuccesses;
    }

    /**
     * set CrawlSuccesses
     *
     * @param int $crawlSuccesses
     *
     * @return CrawlLink
     */
    public function setCrawlSuccesses(int $crawlSuccesses): self
    {
        $this->crawlSuccesses = $crawlSuccesses;

        return $this;
    }

    /**
     * @param int $crawlSuccesses
     *
     * @return CrawlLink
     */
    public function addCrawlSuccesses(int $crawlSuccesses): self
    {
        $this->crawlSuccesses = $this->crawlSuccesses + $crawlSuccesses;

        return $this;
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

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getAbsoluteLink();
    }
}
=======
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use \DateTime;

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
     * @ORM\Column(name="crawl_date", type="datetime", nullable=true)
     */
    private $crawlDate;

    /**
     * Number of times crawled.
     *
     * @var int
     *
     * @ORM\Column(name="crawl_count", type="integer", nullable=false, options={"default"=0})
     */
    private $crawlCount = 0;

    /**
     * Number of new links found at this address.
     *
     * @var int
     *
     * @ORM\Column(name="crawl_successes", type="integer", nullable=false, options={"default"=0})
     */
    private $crawlSuccesses = 0;

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
    public function setLink(string $link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * Get absolute url.
     *
     * @return string
     */
    public function getAbsoluteLink(): string
    {
        return ltrim($this->website->getUrl(), '/') . $this->getLink();
    }

    /**
     * Set crawled
     *
     * @param boolean $crawled
     *
     * @return CrawlLink
     */
    public function setCrawled(bool $crawled)
    {
        $this->crawled = $crawled;

        return $this;
    }

    /**
     * Get crawled
     *
     * @return bool
     */
    public function isCrawled(): bool
    {
        return $this->crawled;
    }

    /**
     * Set crawlDate
     *
     * @param DateTime $crawlDate
     *
     * @return CrawlLink
     */
    public function setCrawlDate(DateTime $crawlDate)
    {
        $this->crawlDate = $crawlDate;

        return $this;
    }

    /**
     * Get crawlDate
     *
     * @return DateTime
     */
    public function getCrawlDate(): DateTime
    {
        return $this->crawlDate;
    }

    /**
     * get CrawlCount
     *
     * @return int
     */
    public function getCrawlCount(): int
    {
        return $this->crawlCount;
    }

    /**
     * set CrawlCount
     *
     * @param int $crawlCount
     *
     * @return CrawlLink
     */
    public function setCrawlCount(int $crawlCount)
    {
        $this->crawlCount = $crawlCount;

        return $this;
    }

    /**
     * add one to crawl count.
     *
     * @return CrawlLink
     */
    public function addCrawlCount()
    {
        $this->crawlCount++;

        return $this;
    }

    /**
     * get CrawlSuccesses
     *
     * @return int
     */
    public function getCrawlSuccesses(): int
    {
        return $this->crawlSuccesses;
    }

    /**
     * set CrawlSuccesses
     *
     * @param int $crawlSuccesses
     *
     * @return CrawlLink
     */
    public function setCrawlSuccesses(int $crawlSuccesses)
    {
        $this->crawlSuccesses = $crawlSuccesses;

        return $this;
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

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getAbsoluteLink();
    }
}
>>>>>>> parent of 00b8745... consolidate
