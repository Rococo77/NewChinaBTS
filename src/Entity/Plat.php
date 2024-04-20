<?php

namespace App\Entity;

use App\Repository\PlatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlatRepository::class)]
class Plat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Nom = null;

    #[ORM\Column(length: 255)]
    private ?string $Description = null;

    #[ORM\Column]
    private ?float $PrixUnit = null;

    #[ORM\Column(nullable: true)]
    private ?int $StockQtt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $PeremptionDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Allergen = null;

    #[ORM\ManyToOne(inversedBy: 'Plats')]
    private ?Panier $panier = null;

    /**
     * @var Collection<int, Commande>
     */
    #[ORM\ManyToMany(targetEntity: Commande::class, mappedBy: 'Plat')]
    private Collection $commandes;

    #[ORM\ManyToOne(inversedBy: 'Plat')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Region $region = null;

    /**
     * @var Collection<int, Ingredient>
     */
    #[ORM\ManyToMany(targetEntity: Ingredient::class, mappedBy: 'Plat')]
    private Collection $ingredients;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
        $this->ingredients = new ArrayCollection();
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

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): static
    {
        $this->panier = $panier;

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
}
