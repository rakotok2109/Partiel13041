<?php

namespace App\Controller;

use App\Entity\Election;
use App\Entity\Bulletin;
use App\Form\BulletinType;
use App\Repository\BulletinRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BulletinController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/bulletin', name: 'app_bulletin')]
    public function index(): Response
    {
        return $this->render('bulletin/index.html.twig', [
            'controller_name' => 'BulletinController',
        ]);
    }


    /*Méthode pour créer un nouveau bulletin dans une élection*/

    #[Route('/election/{id}/bulletin/new', name: 'new_bulletin')]
    public function createBulletin(Request $request, int $id): Response
    {
        $election = $this->entityManager->getRepository(Election::class)->find($id); /*Récupération de l'id de l'élection*/

        if (!$election) {
            throw $this->createNotFoundException('Election not found'); /*Si l'élection n'existe pas -> message d'erreur*/
        }

        $bulletin = new Bulletin();     /* Initialisation d'un nouveau Bulletin*/
        /* L'utilisateur va créer son bulletin via un formulaire */
        $form = $this->createForm(BulletinType::class, $bulletin);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $bulletin->setElection($election);
            $this->entityManager->persist($bulletin);
            $this->entityManager->flush();

            $this->addFlash('success', 'Bulletin créé avec succès.');
        }

        /* Interface utilisateur pour créer son bulletin grâce au fichier .html.twig*/
        return $this->render('bulletin/create.html.twig', [
            'form' => $form->createView(),
            'election' => $election,
        ]);
    }


    /*Méthode permettant de visualiser l'ensemble des bulletins pour une élection*/
    #[Route('/election/{id}/bulletins', name: 'election_bulletins')]
    public function electionBulletins(int $id): Response
    {
        $election = $this->entityManager->getRepository(Election::class)->find($id);

        if (!$election) {
            throw $this->createNotFoundException('Election not found');
        }

        $bulletins = $this->entityManager->getRepository(Bulletin::class)->findBy(['election' => $election]);

        return $this->render('bulletin/bulletins.html.twig', [
            'election' => $election,
            'bulletins' => $bulletins,
        ]);
    }
}
