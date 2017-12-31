<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
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
    private $lastCrawled;

    /**
     * @var bool
     *
     * @ORM\Column(name="noCrawl", type="boolean")
     */
    private $noCrawl;

    /**
     * @var ArrayCollection|CrawlLink[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\CrawlLink", mappedBy="website")
     */
    private $crawlLinks;

    /**
     * Website constructor.
     */
    public function __construct()
    {
        $this->crawlLinks = new ArrayCollection();
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
    public function setName($name)
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
    public function setUrl($url)
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
     * @param DateTime $lastCrawled
     *
     * @return Website
     */
    public function setLastCrawled($lastCrawled)
    {
        $this->lastCrawled = $lastCrawled;

        return $this;
    }

    /**
     * Get lastCrawled
     *
     * @return DateTime
     */
    public function getLastCrawled()
    {
        return $this->lastCrawled;
    }

    /**
     * Set noCrawl
     *
     * @param boolean $noCrawl
     *
     * @return Website
     */
    public function setNoCrawl($noCrawl)
    {
        $this->noCrawl = $noCrawl;

        return $this;
    }

    /**
     * Get noCrawl
     *
     * @return bool
     */
    public function getNoCrawl()
    {
        return $this->noCrawl;
    }

    /**
     * get CrawlLinks
     *
     * @return ArrayCollection|CrawlLink[]
     */
    public function getCrawlLinks()
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
}

