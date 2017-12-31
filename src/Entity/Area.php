<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Area, any geographical entity.
 *
 * @ApiResource()
 * @ORM\Entity()
 */
class Area
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Name of town.
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128)
     */
    private $name;

    /**
     * Town in which this town is located.
     *
     * @var Area
     *
     * @ORM\ManyToOne(targetEntity="Area", inversedBy="childAreas")
     *
     */
    private $parentArea;

    /**
     * Towns located in this town.
     *
     * @var Area[]
     *
     * @ORM\OneToMany(targetEntity="Area", mappedBy="parentArea")
     */
    private $childAreas;

    /**
     * @var Website[]
     *
     * @ORM\ManyToMany(targetEntity="Website", inversedBy="areas")
     */
    private $websites;


    public function __construct()
    {
        $this->childAreas = new ArrayCollection();
        $this->websites = new ArrayCollection();
    }

    /**
     * get Id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * set Id
     *
     * @param int $id
     *
     * @return Area
     */
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * get Name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * set Name
     *
     * @param string $name
     *
     * @return Area
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * get ParentArea
     *
     * @return Area
     */
    public function getParentArea(): ?Area
    {
        return $this->parentArea;
    }

    /**
     * set ParentArea
     *
     * @param Area $parentArea
     *
     * @return Area
     */
    public function setParentArea(Area $parentArea)
    {
        $this->parentArea = $parentArea;

        return $this;
    }

    /**
     * get ChildAreas
     *
     * @return Area[]
     */
    public function getChildAreas(): array
    {
        return $this->childAreas;
    }

    /**
     * set ChildAreas
     *
     * @param Area[] $childAreas
     *
     * @return Area
     */
    public function setChildAreas(array $childAreas)
    {
        $this->childAreas = $childAreas;

        return $this;
    }

    /**
     * Add child area.
     *
     * @param Area $area
     *
     * @return Area
     */
    public function addChildArea(Area $area)
    {
        if (false === $this->childAreas->contains($area)) {
            $this->childAreas->add($area);
        }

        return $this;
    }

    /**
     * Remove child area.
     *
     * @param Area $area
     *
     * @return Area
     */
    public function removeChildArea(Area $area)
    {
        if ($this->childAreas->contains($area)) {
            $this->childAreas->remove($area);
        }

        return $this;
    }

    /**
     * get Websites
     *
     * @return Website[]
     */
    public function getWebsites(): array
    {
        return $this->websites;
    }

    /**
     * set Websites
     *
     * @param Website[] $websites
     *
     * @return Area
     */
    public function setWebsites(array $websites)
    {
        $this->websites = $websites;

        return $this;
    }

    /**
     * Add Website.
     *
     * @param Website $website
     *
     * @return Area
     */
    public function addWebsite(Website $website)
    {
        if (false === $this->websites->contains($website)) {
            $this->websites->add($website);
        }

        return $this;
    }

    /**
     * Remove Website.
     *
     * @param Website $website
     *
     * @return Area
     */
    public function removeWebsite(Website $website)
    {
        if ($this->websites->contains($website)) {
            $this->websites->remove($website);
        }

        return $this;
    }
}