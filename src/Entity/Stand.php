<?php

namespace App\Entity;

use App\Repository\StandRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StandRepository::class)]
class Stand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $picture;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?DateTimeInterface $duration = null;

    #[ORM\ManyToOne(inversedBy: 'stands')]
    private ?Forum $forum = null;

    /**
     * @var Collection<int, Timeslot>
     */
    #[ORM\OneToMany(targetEntity: Timeslot::class, mappedBy: 'stand')]
    private Collection $timeSlots;

    #[ORM\Column(nullable: true)]
    private ?float $note = null;

    /**
     * @var Collection<int, NoteStand>
     */
    #[ORM\OneToMany(targetEntity: NoteStand::class, mappedBy: 'stand')]
    private Collection $notes;

    public function __construct()
    {
        $this->timeSlots = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getDuration(): ?DateTimeInterface
    {
        return $this->duration;
    }

    public function setDuration(DateTimeInterface $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getForum(): ?Forum
    {
        return $this->forum;
    }

    public function setForum(?Forum $forum): static
    {
        $this->forum = $forum;

        return $this;
    }

    /**
     * @return Collection<int, Timeslot>
     */
    public function getTimeSlots(): Collection
    {
        return $this->timeSlots;
    }

    public function addTimeSlot(Timeslot $timeSlot): static
    {
        if (!$this->timeSlots->contains($timeSlot)) {
            $this->timeSlots->add($timeSlot);
            $timeSlot->setStand($this);
        }

        return $this;
    }

    public function removeTimeSlot(Timeslot $timeSlot): static
    {
        if ($this->timeSlots->removeElement($timeSlot)) {
            // set the owning side to null (unless already changed)
            if ($timeSlot->getStand() === $this) {
                $timeSlot->setStand(null);
            }
        }

        return $this;
    }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(?float $note): static
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return Collection<int, NoteStand>
     */

    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(NoteStand $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setStand($this);
        }

        return $this;
    }

    public function removeNote(NoteStand $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getStand() === $this) {
                $note->setStand(null);
            }
        }

        return $this;
    }

    public function getAverageRating(): float
    {
        $total = 0;
        $count = 0;

        foreach ($this->notes as $note) {
            $total += $note->getNote();
            $count++;
        }

        return $count > 0 ? round($total / $count, 1) : 0;
    }
}
