<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['plat.index','ingredient.index'  ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['plat.index','ingredient.index'  ])]

    private ?string $Nom = null;

    /**
     * @var Collection<int, Plat>
     */
    #[ORM\ManyToMany(targetEntity: Plat::class, inversedBy: 'ingredients')]
    private Collection $Plat;

    /**
     * @var Collection<int, IngrStock>
     */
    #[ORM\ManyToMany(targetEntity: IngrStock::class, inversedBy: 'ingredients')]
    private Collection $Stock;

    public function __construct()
    {
        $this->Plat = new ArrayCollection();
        $this->Stock = new ArrayCollection();
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

}
