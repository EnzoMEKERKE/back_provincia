<?php

namespace App\Entity;

use App\Enum\Difficulte;
use App\Enum\Niveau;
use App\Repository\CoursRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', enumType: Niveau::class)]
    private ?Niveau $niveau = null;

    #[ORM\Column(type: 'string', enumType: Difficulte::class)]
    private ?Difficulte $difficulte = null;

    #[ORM\Column(length: 255)]
    private ?string $langage = null;

    #[ORM\Column]
    private ?int $duree = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNiveau(): ?Niveau
    {
        return $this->niveau;
    }

    public function setNiveau(Niveau $niveau): static
    {
        $this->niveau = $niveau;

        return $this;
    }

    public function getDifficulte(): ?Difficulte
    {
        return $this->difficulte;
    }

    public function setDifficulte(Difficulte $difficulte): static
    {
        $this->difficulte = $difficulte;

        return $this;
    }

    public function getLangage(): ?string
    {
        return $this->langage;
    }

    public function setLangage(string $langage): static
    {
        $this->langage = $langage;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }
}
