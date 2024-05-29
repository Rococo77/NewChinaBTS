<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['recipe.show','ingredient.index','ingredient.show','compo.index'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['recipe.show','ingredient.index','ingredient.show','compo.index'])]
    private ?string $Nom = null;

    /**
     * @var Collection<int, Plat>
     */
    #[ORM\ManyToMany(targetEntity: Plat::class, inversedBy: 'ingredients')]
    #[Groups(['recipe.show','ingredient.index'])]
    private Collection $Plat;

    /**
     * @var Collection<int, IngrStock>
     */
    #[ORM\ManyToMany(targetEntity: IngrStock::class, inversedBy: 'ingredients')]
    private Collection $Stock;

    /**
     * @var Collection<int, Fournisseur>
     */
    #[ORM\ManyToMany(targetEntity: Fournisseur::class, mappedBy: 'ingredients')]
    #[Groups(['ingredient.show'])]
    private Collection $fournisseurs;

    #[ORM\Column]
    private ?bool $allergen = null;

    #[ORM\ManyToOne(inversedBy: 'ingredients')]
    private ?Label $label = null;

    public function __construct()
    {
        $this->Plat = new ArrayCollection();
        $this->Stock = new ArrayCollection();
        $this->fournisseurs = new ArrayCollection();
    }

    /**
     * @return Collection|Fournisseur[]
     */
    public function getFournisseurs(): Collection
    {
        return $this->fournisseurs;
    }

    public function addFournisseur(Fournisseur $fournisseur): self
    {
        if (!$this->fournisseurs->contains($fournisseur)) {
            $this->fournisseurs[] = $fournisseur;
        }

        return $this;
    }

    public function removeFournisseur(Fournisseur $fournisseur): self
    {
        $this->fournisseurs->removeElement($fournisseur);

        return $this;
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

    /**
     * @return Collection<int, Plat>
     */
    public function getPlat(): Collection
    {
        return $this->Plat;
    }

    public function addPlat(Plat $plat): static
    {
        if (!$this->Plat->contains($plat)) {
            $this->Plat->add($plat);
        }

        return $this;
    }

    public function removePlat(Plat $plat): static
    {
        $this->Plat->removeElement($plat);

        return $this;
    }

    /**
     * @return Collection<int, IngrStock>
     */
    public function getStock(): Collection
    {
        return $this->Stock;
    }

    public function addStock(IngrStock $stock): static
    {
        if (!$this->Stock->contains($stock)) {
            $this->Stock->add($stock);
        }

        return $this;
    }

    public function removeStock(IngrStock $stock): static
    {
        $this->Stock->removeElement($stock);

        return $this;
    }

    /**
     * @return Collection<int, Fournisseur>
     */

    public function isAllergen(): ?bool
    {
        return $this->allergen;
    }

    public function setAllergen(bool $allergen): static
    {
        $this->allergen = $allergen;

        return $this;
    }

    public function getLabel(): ?Label
    {
        return $this->label;
    }

    public function setLabel(?Label $label): static
    {
        $this->label = $label;

        return $this;
    }

}
