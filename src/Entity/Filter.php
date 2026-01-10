<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FiltersRepository;

#[ORM\Entity(repositoryClass: FiltersRepository::class)]
class Filter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Criteria::class, mappedBy: 'filter', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $criteria;

    public function __construct()
    {
        $this->criteria = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getCriteria(): Collection
    {
        return $this->criteria;
    }

    public function setCriteria(Collection $criteria): void
    {
        $this->criteria = $criteria;
    }

    public function addCriteria(Criteria $criteria): self
    {
        if (!$this->criteria->contains($criteria)) {
            $this->criteria->add($criteria);
            $criteria->setFilter($this);
        }
        return $this;
    }

    public function removeCriteria(Criteria $criteria): void
    {
        if ($this->criteria->removeElement($criteria)) {
            if ($criteria->getFilter() === $this) {
                $criteria->setFilter(null);
            }
        }
    }

    public function addCriterion(Criteria $criterion): static
    {
        if (!$this->criteria->contains($criterion)) {
            $this->criteria->add($criterion);
            $criterion->setFilter($this);
        }

        return $this;
    }

    public function removeCriterion(Criteria $criterion): static
    {
        if ($this->criteria->removeElement($criterion)) {
            // set the owning side to null (unless already changed)
            if ($criterion->getFilter() === $this) {
                $criterion->setFilter(null);
            }
        }

        return $this;
    }
}
