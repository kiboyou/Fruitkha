<?php

namespace App\Entity;

use App\Repository\ApprovisionnementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApprovisionnementRepository::class)]
class Approvisionnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantite_ajouter = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'approvisionnements')]
    private ?Fournisseur $fournisseur = null;

    #[ORM\ManyToOne(inversedBy: 'approvisionnements')]
    private ?Fruits $fruit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantiteAjouter(): ?int
    {
        return $this->quantite_ajouter;
    }

    public function setQuantiteAjouter(int $quantite_ajouter): static
    {
        $this->quantite_ajouter = $quantite_ajouter;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getFournisseur(): ?Fournisseur
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Fournisseur $fournisseur): static
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    public function getFruit(): ?Fruits
    {
        return $this->fruit;
    }

    public function setFruit(?Fruits $fruit): static
    {
        $this->fruit = $fruit;

        return $this;
    }
}
