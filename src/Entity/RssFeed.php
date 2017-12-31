<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * RssFeed
 *
 * @ApiResource()
 * @ORM\Table(name="rss_feed")
 * @ORM\Entity()
 */
class RssFeed
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
     * @ORM\Column(name="url", type="string", length=255, unique=true)
     */
    private $url;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="lastCall", type="datetime", nullable=true)
     */
    private $lastCall;


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
     * Set url
     *
     * @param string $url
     *
     * @return RssFeed
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
     * Set lastCall
     *
     * @param DateTime $lastCall
     *
     * @return RssFeed
     */
    public function setLastCall(DateTime $lastCall = null)
    {
        $this->lastCall = $lastCall;

        return $this;
    }

    /**
     * Get lastCall
     *
     * @return DateTime
     */
    public function getLastCall(): ?DateTime
    {
        return $this->lastCall;
    }
}
