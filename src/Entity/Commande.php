<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['commande:read'])]

    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['commande:read'])]
    private ?\DateTimeInterface $Date_Com = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'commandes')]
    #[Groups(['commande:read'])]
    private Collection $Users;

    /**
     * @var Collection<int, PanierItem>
     */
    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: PanierItem::class, cascade: ['persist', 'remove'])]
    #[Groups(['commande:read'])]
    private Collection $items;

    public function __construct()
    {
        $this->Users = new ArrayCollection();
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCom(): ?\DateTimeInterface
    {
        return $this->Date_Com;
    }

    public function setDateCom(\DateTimeInterface $Date_Com): static
    {
        $this->Date_Com = $Date_Com;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->Users;
    }

    public function addUser(User $user): static
    {
        if (!$this->Users->contains($user)) {
            $this->Users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->Users->removeElement($user);

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
            $this->items->add($item);
            $item->setCommande($this);
        }

        return $this;
    }

    public function removeItem(PanierItem $item): static
    {
        if ($this->items->removeElement($item)) {
            // Set the owning side to null (unless already changed)
            if ($item->getCommande() === $this) {
                $item->setCommande(null);
            }
        }

        return $this;
    }
}
