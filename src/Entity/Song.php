<?php

namespace App\Entity;

use App\Repository\SongRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SongRepository::class)]
class Song
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $artist = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deleted_at = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picturePath = null;

    #[ORM\Column(length: 255)]
    private ?string $audioPath = null;

    #[ORM\ManyToOne(inversedBy: 'relation')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'song', targetEntity: Comment::class)]
    private Collection $relation;

    #[ORM\ManyToMany(targetEntity: Playlist::class, inversedBy: 'songs')]
    private Collection $relationWithPlaylist;

    #[ORM\ManyToMany(targetEntity: Album::class, inversedBy: 'songs')]
    private Collection $relationWithAlbum;

    public function __construct()
    {
        $this->relation = new ArrayCollection();
        $this->relationWithPlaylist = new ArrayCollection();
        $this->relationWithAlbum = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getArtist(): ?string
    {
        return $this->artist;
    }

    public function setArtist(?string $artist): self
    {
        $this->artist = $artist;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeInterface $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getPicturePath(): ?string
    {
        return $this->picturePath;
    }

    public function setPicturePath(?string $picturePath): self
    {
        $this->picturePath = $picturePath;

        return $this;
    }

    public function getAudioPath(): ?string
    {
        return $this->audioPath;
    }

    public function setAudioPath(string $audioPath): self
    {
        $this->audioPath = $audioPath;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, comment>
     */
    public function getRelation(): Collection
    {
        return $this->relation;
    }

    public function addRelation(comment $relation): self
    {
        if (!$this->relation->contains($relation)) {
            $this->relation->add($relation);
            $relation->setSong($this);
        }

        return $this;
    }

    public function removeRelation(comment $relation): self
    {
        if ($this->relation->removeElement($relation)) {
            // set the owning side to null (unless already changed)
            if ($relation->getSong() === $this) {
                $relation->setSong(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, playlist>
     */
    public function getRelationWithPlaylist(): Collection
    {
        return $this->relationWithPlaylist;
    }

    public function addRelationWithPlaylist(playlist $relationWithPlaylist): self
    {
        if (!$this->relationWithPlaylist->contains($relationWithPlaylist)) {
            $this->relationWithPlaylist->add($relationWithPlaylist);
        }

        return $this;
    }

    public function removeRelationWithPlaylist(playlist $relationWithPlaylist): self
    {
        $this->relationWithPlaylist->removeElement($relationWithPlaylist);

        return $this;
    }

    /**
     * @return Collection<int, album>
     */
    public function getRelationWithAlbum(): Collection
    {
        return $this->relationWithAlbum;
    }

    public function addRelationWithAlbum(album $relationWithAlbum): self
    {
        if (!$this->relationWithAlbum->contains($relationWithAlbum)) {
            $this->relationWithAlbum->add($relationWithAlbum);
        }

        return $this;
    }

    public function removeRelationWithAlbum(album $relationWithAlbum): self
    {
        $this->relationWithAlbum->removeElement($relationWithAlbum);

        return $this;
    }
}
