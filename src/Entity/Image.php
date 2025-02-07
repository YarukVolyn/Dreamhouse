<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 * @Vich\Uploadable
 */
class Image
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="image", fileNameProperty="image")
     */
    private $imageFile;

    /**
     * @ORM\ManyToOne(targetEntity=RealEstate::class, inversedBy="images", cascade={"persist", "remove"})
     */
    private $realEstate;

    /**
     * @ORM\OneToOne(targetEntity=Article::class, mappedBy="image", cascade={"persist", "remove"})
     */
    private $article;

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getRealEstate(): ?RealEstate
    {
        return $this->realEstate;
    }

    public function setRealEstate(?RealEstate $realEstate): self
    {
        $this->realEstate = $realEstate;

        return $this;
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImageFile($imageFile): self
    {
        $this->imageFile = $imageFile;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        // unset the owning side of the relation if necessary
        if ($article === null && $this->article !== null) {
            $this->article->setImage(null);
        }

        // set the owning side of the relation if necessary
        if ($article !== null && $article->getImage() !== $this) {
            $article->setImage($this);
        }

        $this->article = $article;

        return $this;
    }
}
