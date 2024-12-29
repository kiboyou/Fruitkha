<?php

namespace App\Controller\client\account;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/user/login', name: 'user_login', )]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
         // Si l'utilisateur est déjà connecté, on le redirige vers le tableau de bord
        if ($this->getUser()) {
            return $this->redirectToRoute('app_cart');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/user/deconnexion', name: 'user_logout')]
    public function logout(Security $security): Response
    {
        //verifie si l'user est connecte
        if(!$security->getUser()){
            return $this->redirectToRoute('user_login');
        }
        throw new \Exception('This should never be reached!');
    }
}
