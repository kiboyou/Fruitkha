<?php

declare(strict_types=1);

namespace App\Controller\client;

use App\Classe\Cart;
use App\Entity\Categorie;
use App\Entity\Commande;
use App\Entity\Facture;
use App\Entity\Fruits;
use App\Entity\PanierItem;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Guid\Guid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommandeController extends AbstractController
{
    #[Route('/user/order', name: 'app_commande')]
    public function index(Cart $cart, Security $security): Response
    {
        //verifie si l'user est connecte
        if(!$security->getUser()){
            return $this->redirectToRoute('user_login');
        }

        return $this->render('commande/index.html.twig',[
            'cart' => $cart->getCart(),
            'total' => $cart->getTotalPriceCart(),
            'livraison' => 2
        ]);
    }

    #[Route('/user/order/validate', name: 'app_commande_valide')]
    public function finaliser(Cart $cart, Security $security, Request $request, EntityManagerInterface $entityManager): Response
    {
        //verifie si l'user est connecte
        if(!$security->getUser()){
            return $this->redirectToRoute('user_login');
        }
        if($request->getMethod() !== 'POST'){
            return $this->redirectToRoute('app_cart');
        }

        //recuperer les donnees du formulaire
        $addresse_livraison = $request->request->get('addresse_livraison');
        $mode_paiement = $request->request->get('mode_paiement');

        if (!$addresse_livraison || !$mode_paiement) {
            $this->addFlash('error', 'L\'adresse de livraison et le mode de paiement sont obligatoires.');
            return $this->redirectToRoute('app_cart'); // Remplace cette route par celle correspondant à ton panier
        }

        $statut = ($mode_paiement === "carte") ? "En attente de paiement" : "En attente de livraison";

        $products = $cart->getCart();
        $client = $this->getUser();
        $fraisLivraison = 2;

        // Générer la référence de la commande
        $reference = $this->generateOrderReference();

        // Vérification de l'unicité de la référence dans la base de données
        $existingCommande = $entityManager->getRepository(Commande::class)->findOneBy(['reference' => $reference]);

        // Si la référence existe déjà, générer une nouvelle référence
        while ($existingCommande) {
            $reference = $this->generateOrderReference(); // Re-génère une nouvelle référence
            $existingCommande = $entityManager->getRepository(Commande::class)->findOneBy(['reference' => $reference]);
        }

        //creer la commande
        $commande = new Commande();
        $commande->setClient($client);
        $commande->setDateCommande(new \DateTime());
        $commande->setStatus($statut);
        $commande->setReference($reference);
        $commande->setAddresseLivraison($addresse_livraison);
        $commande->setModePaiement($mode_paiement);
        //enregistrer la commande dans la base de donnees
        $entityManager->persist($commande);

        //Creer la facture associe a la commande
        $facture = new Facture();
        $facture->setMontantTotal($cart->getTotalPriceCart() + $fraisLivraison);
        $facture->setDateEmission(new \DateTime());
        $facture->setStatus("Non Paye");
        $facture->setCommande($commande);

        //enregistrer la facture dans la base de donnees
        $entityManager->persist($facture);

        //enregistrer les produits dans le panierItems
        foreach ($products as $product){
            $fruit = $product['produit'];

            /// Vérifiez si le fruit est géré par l'EntityManager
            if (!$entityManager->contains($fruit)) {
                // Recherchez le fruit dans la base de données pour l'attacher à l'EntityManager
                $fruit = $entityManager->find(Fruits::class, $fruit->getId());
                if (!$fruit) {
                    throw new \RuntimeException('Le produit (fruit) est introuvable dans la base de données.');
                }
            }

            // Vérifiez si la catégorie associée est gérée
            $categorie = $fruit->getCategorie();
            if ($categorie && !$entityManager->contains($categorie)) {
                // Recherchez la catégorie dans la base de données
                $categorie = $entityManager->find(Categorie::class, $categorie->getId());
                if (!$categorie) {
                    throw new \RuntimeException('La catégorie associée au produit est introuvable dans la base de données.');
                }
            }
            $panierItem = new PanierItem();
            $panierItem->setFruit($fruit);
            $panierItem->setQuantite($product['quantity']);
            $panierItem->setPrixSousTotal($product['produit']->getPrix() * $product['quantity']);
            $commande->addPanierItem($panierItem);
            //enregistrer le panierItem dans la base de donnees
            $entityManager->persist($panierItem);
        }

        // Enregistrer les données de la commande dans la base de données
        $entityManager->beginTransaction();
        try {
            $entityManager->flush();
            $entityManager->commit();
        } catch (\Exception $e) {
            $entityManager->rollback();
            throw $e;
        }
        //vider le panier
        $cart->remove();

        // Si le mode de paiement est carte, rediriger vers la page de paiement
        if ($mode_paiement === 'carte') {
            return $this->redirectToRoute('app_payement', ['id_commande' => $commande->getId()]);
        }

        return $this->redirectToRoute("app_commande_recap", ['id' => $commande->getId()]);
    }
    #[Route('/user/order/recap/{id}/{motif}', name: 'app_commande_recap', defaults: ['motif' => null])]
    public function recapitulatif(int $id, EntityManagerInterface $entityManager, $motif,Security $security): Response
    {
        if(!$security->getUser()){
            return $this->redirectToRoute('user_login');
        }
        if($motif === 'annulation'){
            $this->addFlash('info', 'Paiement annulé: Veuillez réessayer');
        }elseif ($motif === 'success') {
            $this->addFlash('success', 'Paiement réussi: Votre commande est confirmée.');
        }elseif ($motif === 'erreur') {
            $this->addFlash('success', 'Paiement échou: Votre paiement a echoué.');
        }
        //recuperer la commande
        $commande = $entityManager->getRepository(Commande::class)->find($id);
        if(!$commande){
            $this->addFlash('error', 'La commande n\'existe pas');
            return $this->redirectToRoute('app_cart');
        }
        $client = $this->getUser();
        if($commande->getClient() !== $client){
            $this->addFlash('error', 'Vous n\'avez pas acces a cette commande');
            return $this->redirectToRoute('app_account');
        }

        return $this->render('commande/recap.html.twig',[
            'commande' => $commande
        ]);
    }

    /**
     * Génère une référence unique pour une commande
     *
     * Format de la référence : CMD-YYYYMMDD-XXXXXXX
     * Exemple : CMD-20241228-3e5bc58f
     *
     * @return string La référence unique de la commande
     */
    private function generateOrderReference(): string
    {
        // Date actuelle
        $date = new \DateTime();

        // Génération d'un UUID v4 unique
        $uuid = Guid::uuid4()->toString();

        // Format de la référence : CMD-YYYYMMDD-XXXXXXX
        return 'CMD-' . $date->format('Ymd') . '-' . substr($uuid, 0, 8);
    }
}
