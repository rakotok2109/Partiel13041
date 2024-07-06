<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ElectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ElectionRepository::class)]
#[ApiResource]
class Election
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $theme = null;

    #[ORM\Column]
    private ?int $quota = null;

    #[ORM\Column]
    private ?int $numberwinners = null;

    /**
     * @var Collection<int, Proposition>
     */
    #[ORM\OneToMany(targetEntity: Proposition::class, mappedBy: 'election')]
    private Collection $propositions;

    /**
     * @var Collection<int, Bulletin>
     */
    #[ORM\OneToMany(targetEntity: Bulletin::class, mappedBy: 'election')]
    private Collection $bulletin;

    public function __construct()
    {
        $this->propositions = new ArrayCollection();
        $this->bulletin = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function getQuota(): ?int
    {
        return $this->quota;
    }

    public function setQuota(int $quota): static
    {
        $this->quota = $quota;

        return $this;
    }

    public function getNumberwinners(): ?int
    {
        return $this->numberwinners;
    }

    public function setNumberwinners(int $numberwinners): static
    {
        $this->numberwinners = $numberwinners;

        return $this;
    }

    /**
     * @return Collection<int, Proposition>
     */
    public function getPropositions(): Collection
    {
        return $this->propositions;
    }

    public function addProposition(Proposition $proposition): static
    {
        if (!$this->propositions->contains($proposition)) {
            $this->propositions->add($proposition);
            $proposition->setElection($this);
        }

        return $this;
    }

    public function removeProposition(Proposition $proposition): static
    {
        if ($this->propositions->removeElement($proposition)) {
            // set the owning side to null (unless already changed)
            if ($proposition->getElection() === $this) {
                $proposition->setElection(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Bulletin>
     */
    public function getBulletin(): Collection
    {
        return $this->bulletin;
    }

    public function addBulletin(Bulletin $bulletin): static
    {
        if (!$this->bulletin->contains($bulletin)) {
            $this->bulletin->add($bulletin);
            $bulletin->setElection($this);
        }

        return $this;
    }

    public function removeBulletin(Bulletin $bulletin): static
    {
        if ($this->bulletin->removeElement($bulletin)) {
            // set the owning side to null (unless already changed)
            if ($bulletin->getElection() === $this) {
                $bulletin->setElection(null);
            }
        }

        return $this;
    }
}
