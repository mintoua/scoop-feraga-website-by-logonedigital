<?php

namespace App\Entity;

use App\Repository\CategoryPictureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryPictureRepository::class)]
class CategoryPicture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;


    #[ORM\Column(length: 255)]
    private ?string $designation = null;

    #[ORM\OneToMany(mappedBy: 'categoryPicture', targetEntity: FarmPictures::class, orphanRemoval: true)]
    private Collection $farmPictures;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    public function __construct()
    {
        $this->farmPictures = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->designation;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * @return Collection<int, FarmPictures>
     */
    public function getFarmPictures(): Collection
    {
        return $this->farmPictures;
    }

    public function addFarmPicture(FarmPictures $farmPicture): self
    {
        if (!$this->farmPictures->contains($farmPicture)) {
            $this->farmPictures[] = $farmPicture;
            $farmPicture->setCategoryPicture($this);
        }

        return $this;
    }

    public function removeFarmPicture(FarmPictures $farmPicture): self
    {
        if ($this->farmPictures->removeElement($farmPicture)) {
            // set the owning side to null (unless already changed)
            if ($farmPicture->getCategoryPicture() === $this) {
                $farmPicture->setCategoryPicture(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
