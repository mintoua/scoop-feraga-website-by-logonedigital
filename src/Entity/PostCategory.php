<?php

namespace App\Entity;

use App\Repository\PostCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostCategoryRepository::class)]
class PostCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255 , unique: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'postCategory', targetEntity: Posts::class, orphanRemoval: true)]
    private Collection $posts;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $category_description = null;





    public function __construct()
    {
        $this->posts = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }



    /**
     * @return Collection<int, Posts>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Posts $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setPostCategory($this);
        }

        return $this;
    }

    public function removePost(Posts $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getPostCategory() === $this) {
                $post->setPostCategory(null);
            }
        }

        return $this;

    }

    public function __toString() {
        return $this->name;
    }

    public function getCategoryDescription(): ?string
    {
        return $this->category_description;
    }

    public function setCategoryDescription(?string $category_description): self
    {
        $this->category_description = $category_description;

        return $this;
    }

}
