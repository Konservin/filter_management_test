<?php
namespace App\Entity;

use App\Repository\FilterSubtypesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FilterSubtypesRepository::class)]
class FilterSubtypes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: FilterTypes::class, inversedBy: "subtypes")]
    #[ORM\JoinColumn(name: "type_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private FilterTypes $type;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $name = null;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
