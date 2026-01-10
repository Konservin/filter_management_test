<?php

namespace App\Entity;

use App\Repository\FilterSubtypesRepository;
use App\Repository\TypesSubtypesAssocRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypesSubtypesAssocRepository::class)]
class TypesSubtypesAssoc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: FilterTypes::class, inversedBy: "subtypes")]
    #[ORM\JoinColumn(name: "type", referencedColumnName: "id", onDelete: "CASCADE")]
    private FilterTypes $type;

    #[ORM\ManyToOne(targetEntity: FilterSubtypes::class)]
    #[ORM\JoinColumn(name: "subtype", referencedColumnName: "id", onDelete: "CASCADE")]
    private FilterSubtypes $subtype;

    public function getSubtype(): FilterSubtypes
    {
        return $this->subtype;
    }
    public function setSubtype(FilterSubtypes $subtype): self
    {
        $this->subtype = $subtype;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getType(): FilterTypes
    {
        return $this->type;
    }

    public function setType(FilterTypes $type): void
    {
        $this->type = $type;
    }

}
