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
    private ?string $pictureFileName = null;

    #[ORM\Column(length: 255)]
    private ?string $audioFileName = null;

    #[ORM\ManyToOne(inversedBy: 'relation')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'song', targetEntity: Comment::class)]
    private Collection $relation;

    #[ORM\ManyToMany(targetEntity: Playlist::class, inversedBy: 'songs')]
    private Collection $relationWithPlaylist;

    #[ORM\ManyToMany(targetEntity: Album::class, inversedBy: 'songs')]
    private Collection $relationWithAlbum;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'favoris')]
    private Collection $favoris;

    public function __construct()
    {
        $this->relation = new ArrayCollection();
        $this->relationWithPlaylist = new ArrayCollection();
        $this->relationWithAlbum = new ArrayCollection();
        $this->created_at=new \DateTimeImmutable();
        $this->favoris = new ArrayCollection();
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

    public function getPictureFileName(): ?string
    {
        return $this->pictureFileName;
    }

    public function setPictureFileName(?string $pictureFileName): self
    {
        $this->pictureFileName = $pictureFileName;

        return $this;
    }

    public function getAudioFileName(): ?string
    {
        return $this->audioFileName;
    }

    public function setAudioFileName(string $audioFileName): self
    {
        $this->audioFileName = $audioFileName;

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

    /**
     * @return Collection<int, User>
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(User $favori): self
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris->add($favori);
        }

        return $this;
    }

    public function removeFavori(User $favori): self
    {
        $this->favoris->removeElement($favori);

        return $this;
    }
}
