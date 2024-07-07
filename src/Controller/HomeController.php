<?php

namespace App\Controller;

use App\Entity\Bulletin;
use App\Entity\Election;
use App\Entity\Proposition;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        // Récupère toutes les élections depuis la base de données
        $elections = $this->entityManager->getRepository(Election::class)->findAll();

        /* Interface utilisateur pour la page d'accueil grâce au fichier .html.twig*/
        return $this->render('home/index.html.twig', [
            'elections' => $elections,
        ]);
    }
}
