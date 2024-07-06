<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\BulletinRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BulletinRepository::class)]
#[ApiResource]
class Bulletin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $choice = [];

    #[ORM\ManyToOne(inversedBy: 'bulletin')]
    private ?Election $election = null;

    /**
     * @var Collection<int, Proposition>
     */
    #[ORM\ManyToMany(targetEntity: Proposition::class, inversedBy: 'bulletins')]
    private Collection $proposition;

    public function __construct()
    {
        $this->proposition = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChoice(): array
    {
        return $this->choice;
    }

    public function setChoice(array $choice): static
    {
        $this->choice = $choice;

        return $this;
    }

    public function getElection(): ?Election
    {
        return $this->election;
    }

    public function setElection(?Election $election): static
    {
        $this->election = $election;

        return $this;
    }

    /**
     * @return Collection<int, Proposition>
     */
    public function getProposition(): Collection
    {
        return $this->proposition;
    }

    public function addProposition(Proposition $proposition): static
    {
        if (!$this->proposition->contains($proposition)) {
            $this->proposition->add($proposition);
        }

        return $this;
    }

    public function removeProposition(Proposition $proposition): static
    {
        $this->proposition->removeElement($proposition);

        return $this;
    }
}
