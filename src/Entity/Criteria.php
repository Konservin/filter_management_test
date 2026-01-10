<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CriteriaRepository;

#[ORM\Entity(repositoryClass: CriteriaRepository::class)]
class Criteria
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Filter::class, inversedBy: 'criteria')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Filter $filter = null;

    #[ORM\ManyToOne(targetEntity: FilterTypes::class)]
    #[ORM\JoinColumn(nullable: false)]
    private FilterTypes $type;

    #[ORM\ManyToOne(targetEntity: FilterSubtypes::class)]
    #[ORM\JoinColumn(nullable: false)]
    private FilterSubtypes $subtype;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $value = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getFilter(): ?Filter
    {
        return $this->filter;
    }

    public function setFilter(Filter $filter): void
    {
        $this->filter = $filter;
    }

    public function getType(): FilterTypes
    {
        return $this->type;
    }

    public function setType(FilterTypes $type): void
    {
        $this->type = $type;
    }

    public function getSubtype(): FilterSubtypes
    {
        return $this->subtype;
    }

    public function setSubtype(FilterSubtypes $subtype): void
    {
        $this->subtype = $subtype;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }
}
