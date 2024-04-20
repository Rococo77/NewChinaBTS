<?php

namespace App\Entity;

use App\Repository\PlatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PlatRepository::class)]
class Plat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['plat.index'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]

    #[Groups(['plat.index'])]
    private ?string $Nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(['plat.index'])]
    private ?string $Description = null;

    #[ORM\Column]
    #[Groups(['plat.index'])]
    private ?float $PrixUnit = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['plat.index'])]
    private ?int $StockQtt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['plat.index'])]
    private ?\DateTimeInterface $PeremptionDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['plat.index'])]
    private ?string $Allergen = null;

    

    /**
     * @var Collection<int, Commande>
     */
    #[ORM\ManyToMany(targetEntity: Commande::class, mappedBy: 'Plat')]
    private Collection $commandes;

    #[ORM\ManyToOne(inversedBy: 'Plat')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['plat.index'])]
    private ?Region $region = null;

    /**
     * @var Collection<int, Ingredient>
     */
    #[ORM\ManyToMany(targetEntity: Ingredient::class, mappedBy: 'Plat')]
    #[Groups(['plat.index'])]
    private Collection $ingredients;

    /**
     * @var Collection<int, Panier>
     */
    #[ORM\OneToMany(targetEntity: Panier::class, mappedBy: 'Plat')]
    private Collection $paniers;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
        $this->ingredients = new ArrayCollection();
        $this->paniers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    public function getPrixUnit(): ?float
    {
        return $this->PrixUnit;
    }

    public function setPrixUnit(float $PrixUnit): static
    {
        $this->PrixUnit = $PrixUnit;

        return $this;
    }

    public function getStockQtt(): ?int
    {
        return $this->StockQtt;
    }

    public function setStockQtt(?int $StockQtt): static
    {
        $this->StockQtt = $StockQtt;

        return $this;
    }

    public function getPeremptionDate(): ?\DateTimeInterface
    {
        return $this->PeremptionDate;
    }

    public function setPeremptionDate(?\DateTimeInterface $PeremptionDate): static
    {
        $this->PeremptionDate = $PeremptionDate;

        return $this;
    }

    public function getAllergen(): ?string
    {
        return $this->Allergen;
    }

    public function setAllergen(?string $Allergen): static
    {
        $this->Allergen = $Allergen;

        return $this;
    }

   

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->addPlat($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            $commande->removePlat($this);
        }

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): static
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return Collection<int, Ingredient>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(Ingredient $ingredient): static
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients->add($ingredient);
            $ingredient->addPlat($this);
        }

        return $this;
    }

    public function removeIngredient(Ingredient $ingredient): static
    {
        if ($this->ingredients->removeElement($ingredient)) {
            $ingredient->removePlat($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Panier>
     */
    public function getPaniers(): Collection
    {
        return $this->paniers;
    }

    public function addPanier(Panier $panier): static
    {
        if (!$this->paniers->contains($panier)) {
            $this->paniers->add($panier);
            $panier->setPlat($this);
        }

        return $this;
    }

    public function removePanier(Panier $panier): static
    {
        if ($this->paniers->removeElement($panier)) {
            // set the owning side to null (unless already changed)
            if ($panier->getPlat() === $this) {
                $panier->setPlat(null);
            }
        }

        return $this;
    }
}
