<?php

namespace App\Entity;

use App\Repository\TelefonBoxRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
// edit
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: TelefonBoxRepository::class)]
class TelefonBox
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $start_time = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $end_time = null;

    #[ORM\ManyToOne(inversedBy: 'telefonBoxes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_id = null;

    #[ORM\ManyToOne(inversedBy: 'telefonBoxes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status_id = null;

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

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->start_time;
    }

    public function setStartTime(\DateTimeInterface $start_time): static
    {
        $this->start_time = $start_time;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->end_time;
    }

    public function setEndTime(\DateTimeInterface $end_time): static
    {
        $this->end_time = $end_time;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getStatusId(): ?Status
    {
        return $this->status_id;
    }

    public function setStatusId(?Status $status_id): static
    {
        $this->status_id = $status_id;

        return $this;
    }

    // edited
    public function getCompanyName(): ?string
    {
        return $this->getUserId()?->getCompanyId()?->getCompanyName();
    }
     /**
     * Optional extra check if you need more complex logic:
     */
    #[Assert\Callback]
    public function validateTimeOrder(ExecutionContextInterface $context): void
    {
        if ($this->start_time && $this->end_time && $this->start_time >= $this->end_time) {
            $context
                ->buildViolation('Start time must be before end time.')
                ->atPath('start_time')
                ->addViolation();
        }
    }

    public function __toString(): string
    {
        // Make sure this returns something valid—e.g. the title:
        return (string) $this->title;
    }
}
