<?php

namespace App\Entity;

use App\Repository\RegionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: RegionRepository::class)]
class Region
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['region.index' ,'plat.index'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['region.index' ,'plat.index'])]

    private ?string $Nom = null;

    /**
     * @var Collection<int, Plat>
     */
    #[ORM\OneToMany(targetEntity: Plat::class, mappedBy: 'region', orphanRemoval: true)]
    private Collection $Plat;

    public function __construct()
    {
        $this->Plat = new ArrayCollection();
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
            $plat->setRegion($this);
        }

        return $this;
    }

    public function removePlat(Plat $plat): static
    {
        if ($this->Plat->removeElement($plat)) {
            // set the owning side to null (unless already changed)
            if ($plat->getRegion() === $this) {
                $plat->setRegion(null);
            }
        }

        return $this;
    }
}
