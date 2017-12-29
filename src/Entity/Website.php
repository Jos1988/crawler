<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Website
 *
 * @ApiResource
 * @ORM\Entity(repositoryClass="App\Repository\WebsiteRepository")
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
     * @ORM\Column(name="domain", type="string", length=255, unique=true)
     */
    private $domain;

    /**
     * @var \DateTime
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
     * Get id
     *
     * @return int
     */
    public function getId()
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set domain
     *
     * @param string $domain
     *
     * @return Website
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set lastCrawled
     *
     * @param \DateTime $lastCrawled
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
     * @return \DateTime
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
     */
    public function addCrawlLink(CrawlLink $crawlLink)
    {
        if (false === $this->crawlLinks->contains($crawlLink)) {
            $this->crawlLinks->add($crawlLink);
        }
    }

    /**
     * Remove CrawlLink.
     *
     * @param CrawlLink $crawlLink
     */
    public function removeCrawlLink(CrawlLink $crawlLink)
    {
        if ($this->crawlLinks->contains($crawlLink)) {
            $this->crawlLinks->remove($crawlLink);
        }
    }
}

