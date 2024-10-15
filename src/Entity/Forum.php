<?php

namespace App\Entity;

use App\Repository\ForumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ForumRepository::class)]
class Forum
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'forums')]
    private ?User $user = null;

    #[ORM\Column(length: 100)]
    private ?string $title = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $picture = null;

    #[ORM\Column(length: 300)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    /**
     * @var Collection<int, Stand>
     */
    #[ORM\OneToMany(targetEntity: Stand::class, mappedBy: 'forum')]
    private Collection $stands;

    /**
     * @var Collection<int, NoteForum>
     */
    #[ORM\OneToMany(targetEntity: NoteForum::class, mappedBy: 'forum')]
    private Collection $notes;

    public function __construct()
    {
        $this->stands = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicture($picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection<int, Stand>
     */
    public function getStands(): Collection
    {
        return $this->stands;
    }

    public function addStand(Stand $stand): static
    {
        if (!$this->stands->contains($stand)) {
            $this->stands->add($stand);
            $stand->setForum($this);
        }

        return $this;
    }

    public function removeStand(Stand $stand): static
    {
        if ($this->stands->removeElement($stand)) {
            // set the owning side to null (unless already changed)
            if ($stand->getForum() === $this) {
                $stand->setForum(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, NoteForum>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(NoteForum $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setForum($this);
        }

        return $this;
    }

    public function removeNote(NoteForum $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getForum() === $this) {
                $note->setForum(null);
            }
        }

        return $this;
    }

    public function getAverageNoteofStands(): float
    {
        $total = 0;
        $count = 0;
        foreach ($this->stands as $stand) {
            $total += $stand->getNote();
            $count++;
        }
        if ($count === 0) {
            return 0;
        }
        return $total / $count;
    }
}
