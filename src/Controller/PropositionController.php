<?php

namespace App\Controller;

use App\Entity\Election;
use App\Entity\Proposition;
use App\Form\PropositionType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PropositionController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/proposition', name: 'app_proposition')]
    public function index(): Response
    {
        return $this->render('proposition/index.html.twig', [
            'controller_name' => 'PropositionController',
        ]);
    }

    /*Méthode pour créer une nouvelle proposition dans une élection*/
    #[Route('/election/{id}/proposition/new', name: 'new_proposition')]
    public function createProposition(Request $request, int $id): Response
    {
        $election = $this->entityManager->getRepository(Election::class)->find($id);

        if (!$election) {
            throw $this->createNotFoundException('Election not found');
        }

        $proposition = new Proposition();/* Initialisation d'une nouvelle Proposition*/
        /* L'utilisateur va créer sa proposition via un formulaire */
        $form = $this->createForm(PropositionType::class, $proposition);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $proposition->setElection($election);
            $this->entityManager->persist($proposition);
            $this->entityManager->flush();

            $this->addFlash('success', 'Proposition créée avec succès.');
        }

        /* Interface utilisateur pour créer sa proposition grâce au fichier .html.twig*/
        return $this->render('proposition/create.html.twig', [
            'form' => $form->createView(),
            'election' => $election,
        ]);
    }
}
