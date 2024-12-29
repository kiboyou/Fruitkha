<?php

namespace App\Twig;

use App\Classe\Cart;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class AppExtensions extends AbstractExtension implements GlobalsInterface
{
    private $cart;
    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }
    public function getFilters()
    {
        return [
            new TwigFilter('price', ([$this, 'formatPrice']))
        ];
    }
    public function formatPrice($number)
    {
        return number_format($number, 2, ',', ' ') . ' €';
    }

    //afficher des variables de façon accessible dans toutes les pages
    public function getGlobals(): array
    {
        return [
            'fullCartQuantity' => $this->cart->fullQuantity(),
        ];
    }
}