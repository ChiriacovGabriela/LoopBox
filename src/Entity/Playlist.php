<?php

namespace App\Entity;

use App\Repository\PlaylistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlaylistRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Playlist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageFileName = null;



    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deleted_at = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\ManyToMany(targetEntity: Song::class, mappedBy: 'relationWithPlaylist')]
    private Collection $songs;

    #[ORM\ManyToOne(inversedBy: 'relationWithPlaylist')]
    private ?User $user = null;

    public function __construct()
    {
        $this->songs = new ArrayCollection();
        //$this->created_at = new \DateTimeImmutable();
    }
    #[ORM\PrePersist]
    public function setCreatedAtValue():void
    {
        $this->created_at = new \DateTime;

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

    public function getImageFileName(): ?string
    {
        return $this->imageFileName;
    }

    public function setImageFileName(?string $imageFileName): self
    {
        $this->imageFileName = $imageFileName;

        return $this;
    }

    public function setUpdated_at(?\DateTimeInterface $updated_at): self

    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
