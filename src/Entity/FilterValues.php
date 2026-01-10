<?php

namespace App\Entity;

use App\Repository\FilterValuesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FilterValuesRepository::class)]
class FilterValues
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: FilterTypes::class)]
    #[ORM\JoinColumn(nullable: false)]
    private FilterTypes $type;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $valueType = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): FilterTypes
    {
        return $this->type;
    }

    public function setType(FilterTypes $type): void
    {
        $this->type = $type;
    }

    public function getValueType(): ?string
    {
        return $this->valueType;
    }

    public function setValueType(?string $valueType): void
    {
        $this->valueType = $valueType;
    }
}
