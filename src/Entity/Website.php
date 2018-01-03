<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Website
 *
 * @ApiResource
 * @ORM\Entity()
 */
class Website
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
     * @ORM\Column(name="name", type="string", length=100, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, unique=true)
     */
    private $url;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="lastCrawled", type="datetime", nullable=true)
     */
    private $lastCrawled = null;

    /**
     * @var bool
     *
     * @ORM\Column(name="crawl", type="boolean")
     */
    private $crawl = true;

    /**
     * @var ArrayCollection|CrawlLink[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\CrawlLink", mappedBy="website")
     */
    private $crawlLinks;

    /**
     * @var ArrayCollection|Article[]
     *
     * @ORM\OneToMany(targetEntity="Article", mappedBy="website")
     */
    private $articles;

    /**
     * @var ArrayCollection|RssFeed[]
     *
     * @ORM\OneToMany(targetEntity="RssFeed", mappedBy="website")
     */
    private $rssFeeds;

    /**
     * @var ArrayCollection[]|Area[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Area", mappedBy="websites")
     */
    private $areas;

    /**
     * Website constructor.
     */
    public function __construct()
    {
        $this->crawlLinks = new ArrayCollection();
        $this->areas = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Website
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Website
     */
    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Set lastCrawled
     *
     * @param DateTime|null $lastCrawled
     *
     * @return Website
     */
    public function setLastCrawled(DateTime $lastCrawled = null)
    {
        $this->lastCrawled = $lastCrawled;

        return $this;
    }

    /**
     * Get lastCrawled
     *
     * @return DateTime
     */
    public function getLastCrawled(): DateTime
    {
        return $this->lastCrawled;
    }

    /**
     * is Crawl
     *
     * @return bool
     */
    public function isCrawl(): bool
    {
        return $this->crawl;
    }

    /**
     * set Crawl
     *
     * @param bool $crawl
     *
     * @return Website
     */
    public function setCrawl(bool $crawl)
    {
        $this->crawl = $crawl;

        return $this;
    }

    /**
     * get CrawlLinks
     *
     * @return ArrayCollection|CrawlLink[]
     */
    public function getCrawlLinks(): Collection
    {
        return $this->crawlLinks;
    }

    /**
     * set CrawlLinks
     *
     * @param ArrayCollection|CrawlLink[] $crawlLinks
     *
     * @return Website
     */
    public function setCrawlLinks(ArrayCollection $crawlLinks)
    {
        $this->crawlLinks = $crawlLinks;

        return $this;
    }

    /**
     * Add new CrawlLink.
     *
     * @param CrawlLink $crawlLink
     *
     * @return Website
     */
    public function addCrawlLink(CrawlLink $crawlLink)
    {
        if (false === $this->crawlLinks->contains($crawlLink)) {
            $this->crawlLinks->add($crawlLink);
        }

        return $this;
    }

    /**
     * Remove CrawlLink.
     *
     * @param CrawlLink $crawlLink
     *
     * @return Website
     */
    public function removeCrawlLink(CrawlLink $crawlLink)
    {
        if ($this->crawlLinks->contains($crawlLink)) {
            $this->crawlLinks->remove($crawlLink);
        }

        return $this;
    }

    /**
     * get Articles
     *
     * @return ArrayCollection|Article[]
     */
    public function getArticles(): array
    {
        return $this->articles;
    }

    /**
     * set CrawlLinks
     *
     * @param ArrayCollection|CrawlLink[] $articles
     *
     * @return Website
     */
    public function setArticles(ArrayCollection $articles)
    {
        $this->crawlLinks = $articles;

        return $this;
    }

    /**
     * Add new Article.
     *
     * @param Article $article
     *
     * @return Website
     */
    public function addArticle(Article $article)
    {
        if (false === $this->articles->contains($article)) {
            $this->articles->add($article);
        }

        return $this;
    }

    /**
     * Remove CrawlLink.
     *
     * @param Article $article
     *
     * @return Website
     */
    public function removeArticle(Article $article)
    {
        if ($this->articles->contains($article)) {
            $this->articles->remove($article);
        }

        return $this;
    }

    /**
     * get Articles
     *
     * @return ArrayCollection|RssFeed[]
     */
    public function getRssFeeds(): array
    {
        return $this->rssFeeds;
    }

    /**
     * set CrawlLinks
     *
     * @param ArrayCollection|RssFeed[] $feeds
     *
     * @return Website
     */
    public function setRssFeeds(ArrayCollection $feeds)
    {
        $this->rssFeeds = $feeds;

        return $this;
    }

    /**
     * Add new RssFeed.
     *
     * @param RssFeed $feed
     *
     * @return Website
     */
    public function addRssFeed(RssFeed $feed)
    {
        if (false === $this->rssFeeds->contains($feed)) {
            $this->rssFeeds->add($feed);
        }

        return $this;
    }

    /**
     * Remove RssFeed.
     *
     * @param RssFeed $feed
     *
     * @return Website
     */
    public function removeRssFeed(RssFeed $feed)
    {
        if ($this->rssFeeds->contains($feed)) {
            $this->rssFeeds->remove($feed);
        }

        return $this;
    }

    /**
     * get Areas
     *
     * @return Area[]|ArrayCollection[]
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * set Areas
     *
     * @param Area[]|ArrayCollection[] $areas
     *
     * @return Website
     */
    public function setAreas($areas)
    {
        $this->areas = $areas;

        return $this;
    }

    /**
     * Add new RssFeed.
     *
     * @param Area $area
     *
     * @return Website
     */
    public function addArea(Area $area)
    {
        if (false === $this->areas->contains($area)) {
            $this->areas->add($area);
        }

        return $this;
    }

    /**
     * Remove RssFeed.
     *
     * @param Area $area
     *
     * @return Website
     */
    public function removeArea(Area $area)
    {
        if ($this->areas->contains($area)) {
            $this->areas->remove($area);
        }

        return $this;
    }

}

