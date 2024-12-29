<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    public function __construct(private RequestStack $requestStack)
    {

    }
    public function add($fruit): void
    {
        //appeler la session de symfony
        //$session = $this->requestStack->getSession();
        $cart = $this->requestStack->getSession()->get('cart');

        // Ajouter une quantite +1 a un produit
        if (isset($cart[$fruit->getId()])) {
            $cart[$fruit->getId()] = [
                'produit' => $fruit,
                'quantity' => $cart[$fruit->getId()]['quantity'] + 1
            ];
        }else{
            //ajouter un produit
            $cart[$fruit->getId()] = [
                'produit' => $fruit,
                'quantity' => 1
            ];
        }

        //creer ma session cart
        $this->requestStack->getSession()->set('cart', $cart);

        //dd($this->requestStack->getSession()->get('cart'));
    }
    /*
     * fonction permettant de retourner la quantité total de produit dans le panier
     * */
    public function fullQuantity()
    {
        $cart = $this->getCart();
        $quantity = 0;

        if(!isset($cart)) {
            return $quantity;
        }

        foreach ($cart as $product) {
            $quantity += $product['quantity'];
        }
        return $quantity;
    }
    /*
     * fonction permettant de retourner le prix total du panier
     * */
    public function getTotalPriceCart()
    {
        $cart = $this->getCart();
        $price = 0;

        if(!isset($cart)) {
            return $price;
        }

        foreach ($cart as $product) {
            $price += $product['produit']->getPrix() * $product['quantity'];

        }
        return $price;
    }

    /*
     * fonction permettant de diminuer la quantité d'un produit dans le panier
     * */
    public function decrease($id): void
    {
        $cart = $this->getCart();
        if(($cart[$id]['quantity'] > 1)) {
            $cart[$id]['quantity']--;
        } else {
            unset($cart[$id]);
        }
        $this->requestStack->getSession()->set('cart', $cart);
    }
    /*
     * fonction permettant de supprimer un produit du panier
     * */
    public function removeProduct($id) : void
    {
        $cart = $this->getCart();
        if(isset($cart[$id])) {
            unset($cart[$id]);
        }

        $this->requestStack->getSession()->set('cart', $cart);
    }
    /*
     * fonction permettant de vider le panier
     * */
    public function remove()
    {
        return $this->requestStack->getSession()->remove('cart');
    }


    public function getCart()
    {
        return $this->requestStack->getSession()->get('cart');
    }
}