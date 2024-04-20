<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $Quantité = null;

    #[ORM\ManyToOne(inversedBy: 'paniers')]
    private ?User $Users = null;

    #[ORM\ManyToOne(inversedBy: 'paniers')]
    private ?Plat $Plat = null;

   

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantité(): ?int
    {
        return $this->Quantité;
    }

    public function setQuantité(int $Quantité): static
    {
        $this->Quantité = $Quantité;

        return $this;
    }

    public function getUsers(): ?User
    {
        return $this->Users;
    }

    public function setUsers(?User $Users): static
    {
        $this->Users = $Users;

        return $this;
    }

    public function getPlat(): ?Plat
    {
        return $this->Plat;
    }

    public function setPlat(?Plat $Plat): static
    {
        $this->Plat = $Plat;

        return $this;
    }

    
}
