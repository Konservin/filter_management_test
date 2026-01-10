<?php
namespace App\Entity;

use App\Repository\FilterTypesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FilterTypesRepository::class)]
class FilterTypes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Criteria::class, mappedBy: 'type')]
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

    /**
     * @return Collection|Criteria[]
     */
    public function getCriteria(): Collection
    {
        return $this->criteria;
    }

    public function addCriteria(Criteria $criteria): self
    {
        if (!$this->criteria->contains($criteria)) {
            $this->criteria[] = $criteria;
            $criteria->setType($this);
        }

        return $this;
    }

    public function removeCriteria(Criteria $criteria): self
    {
        if ($this->criteria->removeElement($criteria)) {
            // set the owning side to null (unless already changed)
            if ($criteria->getType() === $this) {
                $criteria->setType(null);
            }
        }

        return $this;
    }

    public function addCriterion(Criteria $criterion): static
    {
        if (!$this->criteria->contains($criterion)) {
            $this->criteria->add($criterion);
            $criterion->setType($this);
        }

        return $this;
    }

    public function removeCriterion(Criteria $criterion): static
    {
        if ($this->criteria->removeElement($criterion)) {
            // set the owning side to null (unless already changed)
            if ($criterion->getType() === $this) {
                $criterion->setType(null);
            }
        }

        return $this;
    }
}

