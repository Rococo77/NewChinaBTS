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

    /**
     * @var Collection<int, Plat>
     */
    #[ORM\OneToMany(targetEntity: Plat::class, mappedBy: 'panier')]
    private Collection $Plats;

    public function __construct()
    {
        $this->Plats = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Plat>
     */
    public function getPlats(): Collection
    {
        return $this->Plats;
    }

    public function addPlat(Plat $plat): static
    {
        if (!$this->Plats->contains($plat)) {
            $this->Plats->add($plat);
            $plat->setPanier($this);
        }

        return $this;
    }

    public function removePlat(Plat $plat): static
    {
        if ($this->Plats->removeElement($plat)) {
            // set the owning side to null (unless already changed)
            if ($plat->getPanier() === $this) {
                $plat->setPanier(null);
            }
        }

        return $this;
    }
}
