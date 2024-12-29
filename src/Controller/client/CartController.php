<?php

declare(strict_types=1);

namespace App\Controller\client;

use App\Classe\Cart;
use App\Repository\FruitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/mon-panier', name: 'app_cart')]
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getCart(),
            'total' => $cart->getTotalPriceCart()
        ]);
    }
    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function add($id, Cart $cart, FruitsRepository $fruitsRepository, Request $request): Response
    {
        $fruit = $fruitsRepository->findOneBy(['id' => $id]);
        $cart->add($fruit);

        $this->addFlash('success', 'Votre produit a bien été ajouté au panier');
        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/cart/decrease/{id}', name: 'app_cart_decrease')]
    public function decrease($id, Cart $cart): Response
    {
        $cart->decrease($id);

        $this->addFlash('success', 'Votre produit a bien été supprimé du panier');
        return $this->redirectToRoute('app_cart');
    }
    #[Route('/cart/remove', name: 'app_cart_remove')]
    public function remove(Cart $cart): Response
    {
        $cart->remove();

        $this->addFlash('warning', 'Votre panier a bien été supprimé');
        return $this->redirectToRoute('app_cart');
    }
    #[Route('/cart/removeproduct/{id}', name: 'app_cart_remove_product')]
    public function removeProduct($id, Cart $cart): Response
    {
        $cart->removeProduct($id);
        $this->addFlash('warning', 'Votre produit a bien été supprimé du panier');
        return $this->redirectToRoute('app_cart');
    }

}
