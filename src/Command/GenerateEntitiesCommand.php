<?php

namespace App\Command;

use App\Entity\Election;
use App\Entity\Proposition;
use App\Entity\Bulletin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generation-entities',
    description: 'Generate random elections, propositions, and bulletins',
)]

/* Création d'élections, de propositions et génération de bulletins de manière aléatoire */
class GenerateEntitiesCommand extends Command
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setName('app:generation-entities')
             ->setDescription('Generate random elections, propositions, and bulletins');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /* Générer des élections aléatoires */
        for ($i = 0; $i < 3; $i++) { /* Génère 3 élections */
            $election = new Election();
            $election->setTheme($this->getRandomTheme());
            $election->setQuota(10);
            $election->setNumberwinners(2);

            $this->entityManager->persist($election);

            /* Générer des propositions aléatoires pour chaque élection */
            for ($j = 0; $j < 5; $j++) { /* Génère 5 propositions par élection */
                $proposition = new Proposition();
                $proposition->setName($this->getRandomPropositionName());

                /* Associer la proposition à l'élection */
                $proposition->setElection($election);

                $this->entityManager->persist($proposition);

                /* Ajouter la proposition à l'élection */
                $election->addProposition($proposition);
            }

            /* Générer des bulletins de vote aléatoires pour chaque élection */
            $numBulletins = mt_rand(20, 50); /* Entre 5 et 20 bulletins par élection */
            for ($k = 0; $k < $numBulletins; $k++) {
                $bulletin = new Bulletin();
                $bulletin->setElection($election);

                /* Sélectionner un nombre aléatoire de propositions pour le bulletin */
                $propositions = $election->getPropositions()->toArray();
                shuffle($propositions);
                $numPropositionsSelected = mt_rand(1, count($propositions));
                $selectedPropositions = array_slice($propositions, 0, $numPropositionsSelected);

                /* Ajouter les propositions sélectionnées à la propriété 'choice' du bulletin */
                $choices = [];
                foreach ($selectedPropositions as $proposition) {
                    $choices[] = $proposition->getName();
                }
                $bulletin->setChoice($choices);

                // Persist and flush each bulletin
                $this->entityManager->persist($bulletin);
            }
        }

        $this->entityManager->flush();

        $output->writeln('Random entities generated successfully.');

        return Command::SUCCESS;
    }

    /* J'ai choisi 4 thèmes et ces thèmes seront générés aléatoirement */
    private function getRandomTheme(): string
    {
        $themes = ['Ligue des Champions', 'Delegues', 'Restaurants', 'Autres'];
        return $themes[array_rand($themes)];
    }

    /* Pour les propositions j'associe des noms et des adjectifs aléatoires */
    private function getRandomPropositionName(): string
    {
        $adjectives = ['Belle', 'Rapide', 'Grande', 'Petite', 'Moderne', 'Ancienne', 'Délicieuse', 'Épicée'];
        $noms = ['Pizza', 'Hamburger', 'Salade', 'Sushi', 'Pâtes', 'Curry', 'Tacos', 'Poisson'];

        $adjective = $adjectives[array_rand($adjectives)];
        $nom = $noms[array_rand($noms)];

        return "{$adjective} {$nom}";
    }
}
