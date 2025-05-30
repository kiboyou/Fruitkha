<?php

declare(strict_types=1);

namespace App\Controller\admin;

use App\Entity\Commande;
use App\Entity\Facture;
use App\Entity\Fruits;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use http\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(EntityManagerInterface $entityManager, Security $security): Response
    {
            if(!$security->getUser()){
                return $this->redirectToRoute('admin_login');
            }
            // Récupérer les comptages
            $clientsCount = $entityManager->getRepository(User::class)->count([]);
            $salesAmount = $entityManager->getRepository(Facture::class)->count([]);
//            $performancePercentage = $this->calculatePerformance(); // Calcul personnalisé
            $productsCount = $entityManager->getRepository(Fruits::class)->count([]);
            $ordersCount = $entityManager->getRepository(Commande::class)->count([]);
//            $paymentsCount = $entityManager->getRepository(Payment::class)->count([]);

            return $this->render('admin/pages/dasboard.html.twig', [
                'clientsCount' => $clientsCount,
                'salesAmount' => $salesAmount,
//                'performancePercentage' => $performancePercentage,
                'productsCount' => $productsCount,
                'ordersCount' => $ordersCount,
//                'paymentsCount' => $paymentsCount,
            ]);
        }
}