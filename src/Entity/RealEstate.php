<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RealEstatesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RealEstatesRepository::class)
 */
class RealEstate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="realEstate", cascade={"persist", "remove"})
     */
    private $images;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $real_estate_operation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $real_estate_type;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $details;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $contact;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contact_type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $price_type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rooms;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $area;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $floor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $object_type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $house_type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bathroom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $heating;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setRealEstate($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getRealEstate() === $this) {
                $image->setRealEstate(null);
            }
        }

        return $this;
    }

    public function getRealEstateOperation(): ?string
    {
        return $this->real_estate_operation;
    }

    public function setRealEstateOperation(string $real_estate_operation): self
    {
        $this->real_estate_operation = $real_estate_operation;

        return $this;
    }

    public function getRealEstateType(): ?string
    {
        return $this->real_estate_type;
    }

    public function setRealEstateType(string $real_estate_type): self
    {
        $this->real_estate_type = $real_estate_type;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function getContactType(): ?string
    {
        return $this->contact_type;
    }

    public function setContactType(?string $contact_type): self
    {
        $this->contact_type = $contact_type;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPriceType(): ?string
    {
        return $this->price_type;
    }

    public function setPriceType(?string $price_type): self
    {
        $this->price_type = $price_type;

        return $this;
    }

    public function getRooms(): ?string
    {
        return $this->rooms;
    }

    public function setRooms(?string $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    public function getArea(): ?string
    {
        return $this->area;
    }

    public function setArea(?string $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getFloor(): ?string
    {
        return $this->floor;
    }

    public function setFloor(?string $floor): self
    {
        $this->floor = $floor;

        return $this;
    }

    public function getObjectType(): ?string
    {
        return $this->object_type;
    }

    public function setObjectType(?string $object_type): self
    {
        $this->object_type = $object_type;

        return $this;
    }

    public function getHouseType(): ?string
    {
        return $this->house_type;
    }

    public function setHouseType(?string $house_type): self
    {
        $this->house_type = $house_type;

        return $this;
    }

    public function getBathroom(): ?string
    {
        return $this->bathroom;
    }

    public function setBathroom(?string $bathroom): self
    {
        $this->bathroom = $bathroom;

        return $this;
    }

    public function getHeating(): ?string
    {
        return $this->heating;
    }

    public function setHeating(?string $heating): self
    {
        $this->heating = $heating;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
