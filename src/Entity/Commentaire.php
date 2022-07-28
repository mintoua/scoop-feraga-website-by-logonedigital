<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Message = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    private ?User $Userid = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    private ?Posts $BlogId = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->Message;
    }

    public function setMessage(string $Message): self
    {
        $this->Message = $Message;

        return $this;
    }

    public function getUserid(): ?User
    {
        return $this->Userid;
    }

    public function setUserid(?User $Userid): self
    {
        $this->Userid = $Userid;

        return $this;
    }

    public function getBlogId(): ?Posts
    {
        return $this->BlogId;
    }

    public function setBlogId(?Posts $BlogId): self
    {
        $this->BlogId = $BlogId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }




}
