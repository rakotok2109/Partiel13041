<?php

namespace App\Controller;

use App\Entity\Election;
use App\Form\ElectionType;
use App\Service\DataStorageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ElectionController extends AbstractController
{
    private $dataStorageService;

    public function __construct(DataStorageService $dataStorageService)
    {
        $this->dataStorageService = $dataStorageService;
    }

    #[Route('/election', name: 'app_election')]
    public function index(): Response
    {
        return $this->render('election/index.html.twig', [
            'controller_name' => 'ElectionController',
        ]);
    }

    #[Route('/election/create', name: 'create_election')]
    public function createElection(Request $request): Response
    {
        $election = new Election();
        $form = $this->createForm(ElectionType::class, $election);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the election and its propositions
            $this->dataStorageService->addElection($election);

            // Optionally handle redirection or success message
            return $this->redirectToRoute('election_success');
        }

        return $this->render('election/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/election/success', name: 'election_success')]
    public function success(): Response
    {
        return new Response('Election created successfully!');
    }
}
