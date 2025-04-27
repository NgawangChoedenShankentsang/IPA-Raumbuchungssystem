<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, TelefonBox>
     */
    #[ORM\OneToMany(targetEntity: TelefonBox::class, mappedBy: 'status_id')]
    private Collection $telefonBoxes;

    public function __construct()
    {
        $this->telefonBoxes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, TelefonBox>
     */
    public function getTelefonBoxes(): Collection
    {
        return $this->telefonBoxes;
    }

    public function addTelefonBox(TelefonBox $telefonBox): static
    {
        if (!$this->telefonBoxes->contains($telefonBox)) {
            $this->telefonBoxes->add($telefonBox);
            $telefonBox->setStatusId($this);
        }

        return $this;
    }

    public function removeTelefonBox(TelefonBox $telefonBox): static
    {
        if ($this->telefonBoxes->removeElement($telefonBox)) {
            // set the owning side to null (unless already changed)
            if ($telefonBox->getStatusId() === $this) {
                $telefonBox->setStatusId(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return (string) $this->name; // or any other property like username
    }
}
