<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $surname = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $preference = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class)]
    private Collection $comment;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Song::class)]
    private Collection $relation;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Playlist::class)]
    private Collection $relationWithPlaylist;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Album::class)]
    private Collection $relationWithAlbum;

    public function __construct()
    {
        $this->comment = new ArrayCollection();
        $this->relation = new ArrayCollection();
        $this->relationWithPlaylist = new ArrayCollection();
        $this->relationWithAlbum = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPreference(): ?string
    {
        return $this->preference;
    }

    public function setPreference(?string $preference): self
    {
        $this->preference = $preference;

        return $this;
    }

    /**
     * @return Collection<int, comment>
     */
    public function getComment(): Collection
    {
        return $this->comment;
    }

    public function addComment(comment $comment): self
    {
        if (!$this->comment->contains($comment)) {
            $this->comment->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(comment $comment): self
    {
        if ($this->comment->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, song>
     */
    public function getRelation(): Collection
    {
        return $this->relation;
    }

    public function addRelation(song $relation): self
    {
        if (!$this->relation->contains($relation)) {
            $this->relation->add($relation);
            $relation->setUser($this);
        }

        return $this;
    }

    public function removeRelation(song $relation): self
    {
        if ($this->relation->removeElement($relation)) {
            // set the owning side to null (unless already changed)
            if ($relation->getUser() === $this) {
                $relation->setUser(null);
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
            $relationWithPlaylist->setUser($this);
        }

        return $this;
    }

    public function removeRelationWithPlaylist(playlist $relationWithPlaylist): self
    {
        if ($this->relationWithPlaylist->removeElement($relationWithPlaylist)) {
            // set the owning side to null (unless already changed)
            if ($relationWithPlaylist->getUser() === $this) {
                $relationWithPlaylist->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Album>
     */
    public function getRelationWithAlbum(): Collection
    {
        return $this->relationWithAlbum;
    }

    public function addRelationWithAlbum(Album $relationWithAlbum): self
    {
        if (!$this->relationWithAlbum->contains($relationWithAlbum)) {
            $this->relationWithAlbum->add($relationWithAlbum);
            $relationWithAlbum->setUser($this);
        }

        return $this;
    }

    public function removeRelationWithAlbum(Album $relationWithAlbum): self
    {
        if ($this->relationWithAlbum->removeElement($relationWithAlbum)) {
            // set the owning side to null (unless already changed)
            if ($relationWithAlbum->getUser() === $this) {
                $relationWithAlbum->setUser(null);
            }
        }

        return $this;
    }
}
