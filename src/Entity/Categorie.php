<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    /**
     * @var Collection<int, Fruits>
     */
    #[ORM\OneToMany(targetEntity: Fruits::class, mappedBy: 'Categorie')]
    private Collection $fruits;

    public function __construct()
    {
        $this->fruits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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

    /**
     * @return Collection<int, Fruits>
     */
    public function getFruits(): Collection
    {
        return $this->fruits;
    }

    public function addFruit(Fruits $fruit): static
    {
        if (!$this->fruits->contains($fruit)) {
            $this->fruits->add($fruit);
            $fruit->setCategorie($this);
        }

        return $this;
    }

    public function removeFruit(Fruits $fruit): static
    {
        if ($this->fruits->removeElement($fruit)) {
            // set the owning side to null (unless already changed)
            if ($fruit->getCategorie() === $this) {
                $fruit->setCategorie(null);
            }
        }

        return $this;
    }
}
