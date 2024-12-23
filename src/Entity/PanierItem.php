<?php

namespace App\Entity;

use App\Repository\PanierItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierItemRepository::class)]
class PanierItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column]
    private ?float $prix_sous_total = null;

    #[ORM\ManyToOne(inversedBy: 'panierItems')]
    private ?Fruits $fruit = null;

    #[ORM\ManyToOne(inversedBy: 'panierItems')]
    private ?Commande $commande = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrixSousTotal(): ?float
    {
        return $this->prix_sous_total;
    }

    public function setPrixSousTotal(float $prix_sous_total): static
    {
        $this->prix_sous_total = $prix_sous_total;

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

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): static
    {
        $this->commande = $commande;

        return $this;
    }
}
