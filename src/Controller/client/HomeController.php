<?php

namespace App\Controller\client;

use App\Entity\Fruits;
use App\Repository\FruitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(FruitsRepository $fruitsRepository): Response
    {
        // Récupérer tous les fruits depuis la base de données
        $fruits = $fruitsRepository->findAll();

        // Passer les fruits à la vue

        return $this->render('home/index.html.twig', [
            'fruits' => $fruits]);
    }


    #[Route('/fruit/{id}', name: 'app_fruit_details')]
    public function show(Fruits $fruit): Response
    {
        // Vérifier si le fruit existe
        if (!$fruit) {
            throw $this->createNotFoundException('Fruit not found');
        }

        // Afficher la page de détails du fruit
        return $this->render('home/show.html.twig', [
            'fruit' => $fruit
        ]);
    }

}
