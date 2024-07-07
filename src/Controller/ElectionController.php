<?php

namespace App\Controller;

use App\Entity\Bulletin;
use App\Entity\Election;
use App\Entity\Proposition;
use App\Form\ElectionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ElectionController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/election', name: 'app_election')]
    public function index(): Response
    {
        return $this->render('election/index.html.twig', [
            'controller_name' => 'ElectionController',
        ]);
    }

    /*Méthode pour créer une nouvelle élection*/
    #[Route('/election/create', name: 'create_election')]
    public function createElection(Request $request): Response
    {
        $election = new Election();/* Initialisation d'une nouvelle Election*/
        /* L'utilisateur va créer son élection via un formulaire */
        $form = $this->createForm(ElectionType::class, $election);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($election);
            $this->entityManager->flush();

            $this->addFlash('success', 'Élection créée avec succès.');
        }

        /* Interface utilisateur pour créer son élection grâce au fichier .html.twig*/
        return $this->render('election/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /*Méthode permettant de visualiser l'ensemble des informations concernant une élection à partir de son id*/
    #[Route('/election/{id}', name: 'infos_election')]
    public function ElectionInfo(int $id): Response
    {
        $election = $this->entityManager->getRepository(Election::class)->find($id);
        $propositions = $this->entityManager->getRepository(Proposition::class)->findBy(['election' => $election]);

        if (!$election) {
            throw $this->createNotFoundException('Election not found');
        }
        
        return $this->render('election/infos.html.twig', [
            'election' => $election,
            'propositions' => $propositions,
        ]);
    }

    
    #[Route('/election/{id}/determine-winners', name: 'election_determine_winners')]
    public function determineWinners(int $id): Response
    {
        $election = $this->entityManager->getRepository(Election::class)->find($id);

        if (!$election) {
            throw $this->createNotFoundException('Election not found');
        }

        $bulletins = $this->entityManager->getRepository(Bulletin::class)->findBy(['election' => $election]);

        $winners = $this->calculateWinners($election, $bulletins);

        return $this->render('election/determine_winners.html.twig', [
            'election' => $election,
            'winners' => $winners,
        ]);
    }

    /*Méthode permettant de déterminer les gagnants pour une élection à partir de quota et de numberwinners soit le nombre de gagnants
    initialisé par le créateur de cette élection*/
    private function calculateWinners(Election $election, array $bulletins): array
    {
        /* Récupération du quota défini par l'utilisateur, le quota correspond au nombre de votes 
        pour une proposition à atteindre pour la déclarer gagnante*/
        $quota = $election->getQuota(); 
        /* Récupération du nombre de gagnants défini par l'utilisateur, 
        le nombre de gagnants  correspond au nombre de propositions élues*/
        $numberWinners = $election->getNumberWinners();

        $votesCount = [];
        /*Chaque Bulletin va être parcouru ainsi que chaque Proposition dans chaque Bulletin*/
        foreach ($bulletins as $bulletin) {
            foreach ($bulletin->getChoice() as $proposition) {
                $propositionId = $proposition->getId();
                if (!isset($votesCount[$propositionId])) {
                    $votesCount[$propositionId] = 0;
                }
                $votesCount[$propositionId]++;
                /*Pour chaque Proposition un compteur est initialisé 
                et à chaque fois qu'il apparait dans un Bulletin le compteur est incrémenté de 1*/
            }
        }

        arsort($votesCount); /*Les propositions sont classées par nombre de votes décroissant*/

        $winners = [];  /* Les gagnants sont stockés dans cette variable*/
        $excessVotes = [];

        /* Sélectionne toutes les propositions qui atteignent ou dépassent le quota*/
        foreach ($votesCount as $propositionId => $votes) {
            if ($votes >= $quota) {
                $winners[] = $propositionId;
                $excessVotes[$propositionId] = $votes - $quota;
                /* $excessVotes récupère la différence entre le nombre de vote 
                pour la proposition et le quota afin de redistribuer les votes à la proposition suivante*/
            }
        }
    
        /* Dans cette boucle les votes excédentaires de chaque proposition gagnante 
        sont redistribués aux propositions suivantes jusqu'à détermination des autres gagnants*/
        foreach ($excessVotes as $propositionId => $excess) {
            /*Les votes excédant vont être distribuer à la proposition suivante*/
            foreach ($bulletins as $bulletin) {
                /* Les propositions dans chaque Bulletin sont récupérés et stocké dans la variable $choices*/
                $choices = $bulletin->getChoice();
                if (!empty($choices) && $choices[0]->getId() == $propositionId) {
                    array_shift($choices); /* Suppression des choix déjà gagnants pour ne pas redistribuer au mauvais*/
                    foreach ($choices as $choice) {
                        $nextPropositionId = $choice->getId();
                        /*Si aucun vote n'avait été attribué à la proposition suivante, un compteur est initialisé*/
                        if (!isset($votesCount[$nextPropositionId])) {
                            $votesCount[$nextPropositionId] = 0;
                        }
                        $votesCount[$nextPropositionId] += $excess;
                        break;
                    }
                }
            }
        }
    
        /* Trie à nouveau les votes restants après redistribution*/
        arsort($votesCount);
    
        /* Continue de sélectionner les gagnants jusqu'à atteindre le nombre souhaité*/
        foreach ($votesCount as $propositionId => $votes) {
            if (count($winners) >= $numberWinners) {
                break;
            }
            if ($votes >= $quota && !in_array($propositionId, $winners)) {
                $winners[] = $propositionId;
            }
        }

    /* Récupération des gagnants */
    $propositionsRepository = $this->entityManager->getRepository(Proposition::class);
    $winningPropositions = [];
    foreach ($winners as $winnerId) {
        $winningPropositions[] = $propositionsRepository->find($winnerId);
    }

    /*Affichage des gagnants */
    return $winningPropositions;
    }
}
