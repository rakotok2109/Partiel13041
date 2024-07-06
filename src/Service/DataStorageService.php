<?php 

namespace App\Service;

use App\Entity\Election;
use App\Entity\Bulletin;
use App\Entity\Proposition;

class DataStorageService
{
    private $elections = [];
    private $bulletins = [];
    private $propositions = [];

    // Methods to handle Election
    public function addElection(Election $election): void
    {
        $this->elections[] = $election;
    }

    public function getElection(int $id): ?Election
    {
        return $this->elections[$id] ?? null;
    }

    public function getAllElections(): array
    {
        return $this->elections;
    }

    // Methods to handle Bulletin
    public function addBulletin(Bulletin $bulletin): void
    {
        $this->bulletins[] = $bulletin;
    }

    public function getBulletin(int $id): ?Bulletin
    {
        return $this->bulletins[$id] ?? null;
    }

    public function getAllBulletins(): array
    {
        return $this->bulletins;
    }

    // Methods to handle Proposition
    public function addProposition(Proposition $proposition): void
    {
        $this->propositions[] = $proposition;
    }

    public function getProposition(int $id): ?Proposition
    {
        return $this->propositions[$id] ?? null;
    }

    public function getAllPropositions(): array
    {
        return $this->propositions;
    }
}