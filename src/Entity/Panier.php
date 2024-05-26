<?php
namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;


#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['panier:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'paniers')]
    #[Groups(['panier:read'])]
    private ?User $Users = null;

    #[ORM\OneToMany(mappedBy: 'panier', targetEntity: PanierItem::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['panier:read'])]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, PanierItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(PanierItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setPanier($this);
        }

        return $this;
    }

    public function removeItem(PanierItem $item): static
    {
        if ($this->items->removeElement($item)) {
            if ($item->getPanier() === $this) {
                $item->setPanier(null);
            }
        }

        return $this;
    }
}


