<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Repository\PostCategoryRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PostCategoryRepository::class)]
#[UniqueEntity(fields: ['name'], message:'cette thématique existe déjà !')]
class PostCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255 , unique: true)]
    private ?string $name = null;
    
     /**
     * @Gedmo\Slug(fields={"name"})
     */
    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'postCategory', targetEntity: Posts::class, orphanRemoval: true)]
    private Collection $posts;

    #[Assert\NotNull('la description ne peux pas être null')]
    #[Assert\Length(
        min: 2,
        max: 150,
        minMessage: 'la description ne doit pas être inférieur à {{ limit }} caractères',
        maxMessage: 'la description ne doit pas être supérieur {{ limit }} caractères',
    )]
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
        return $this->getName();
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
