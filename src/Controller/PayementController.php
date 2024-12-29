<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use http\Client;
use http\Client\Curl\User;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;

class PayementController extends AbstractController
{
    #[Route('/user/order/paiement/{id_commande}', name: 'app_payement')]
    public function index($id_commande, CommandeRepository $commandeRepository,EntityManagerInterface $entityManager, RouterInterface $router): Response
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        //verification de l'url de base
        $serverHost = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'];

        // S'assurer que le bon protocole est utilisé (http ou https)
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';

        // Si l'utilisateur est sur localhost ou 127.0.0.1, on utilise le même hôte
        // Sinon, on utilise le domaine de production configuré dans .env
        $YOUR_DOMAIN = $protocol . '://' . $serverHost;

        //$YOUR_DOMAIN = $_ENV['DOMAIN_NAME'];
        $commande = $commandeRepository->findOneBy([
            'id' => $id_commande,
            'client' => $this->getUser()
        ]);

        if(!$commande){
            return $this->redirectToRoute('app_home');
        }

        $product_for_stripe = [];

        foreach ($commande->getPanierItems() as $item) {
            $product_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => number_format(($item->getPrixSousTotal() / $item->getQuantite()) * 100, 0, '', ''),
                    'product_data' => [
                        'name' => $item->getFruit()->getNom(),
                        'images' => [
                            $YOUR_DOMAIN . '/uploads/fruit_illustrations/' . $item->getFruit()->getIllustration()
                        ],
                    ],
                ],
                'quantity' => $item->getQuantite(),
            ];
        }

        $product_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => 200,
                'product_data' => [
                    'name' => 'Frais de livraison'
                ],
            ],
            'quantity' => 1,
        ];

        //generer l'url de cancel
        $cancel_url = $router->generate('app_commande_recap',[
            'id' => $id_commande,
            'motif' => 'annulation'
        ], RouterInterface::ABSOLUTE_URL);

        $checkout_session = Session::create([
            /*'customer_email' => $this->getUser()->getEmail(),*/
          'line_items' => [[
            $product_for_stripe,
          ]],
          'mode' => 'payment',
          'success_url' => $YOUR_DOMAIN . '/user/order/paiement/success/{CHECKOUT_SESSION_ID}',
          'cancel_url' => $cancel_url,
        ]);

        $commande->setStripeSessionId($checkout_session->id);
        $entityManager->flush();

        return $this->redirect($checkout_session->url);
    }

    #[Route('/user/order/paiement/success/{id_session_stripe}', name: 'app_payement_success')]
    public function success($id_session_stripe,  CommandeRepository $commandeRepository, EntityManagerInterface $entityManager): Response
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $commande = $commandeRepository->findOneBy([
            'stripe_session_id' => $id_session_stripe,
            'client' => $this->getUser()
        ]);

        $checkout_session = Session::retrieve($id_session_stripe);

        // Si la commande n'existe pas, rediriger l'utilisateur vers une page d'erreur
        if (!$commande) {
            $this->addFlash('error', 'Commande non trouvée ou déjà traitée.');
            return $this->redirectToRoute('app_test_index'); // ou une autre route d'erreur
        }

        if ($checkout_session->status !== 'complete') {
            // Si le paiement n'a pas été effectué avec succès, tu peux rediriger avec un message d'erreur
            $this->addFlash('error', 'Le paiement a échoué. Veuillez réessayer.');
            return $this->redirectToRoute('app_commande_recap', [
                'id' => $commande->getId(),
                'motif' => 'erreur'
            ]);
        }

        // Mettre à jour le statut de la commande
        $commande->setStatus('En attente de livraison');
        $commande->getFacture()->setStatus('Payée');

        $entityManager->flush();

        return $this->redirectToRoute('app_commande_recap', [
            'id' => $commande->getId(),
            'motif' => 'success'
        ]);
    }
}
