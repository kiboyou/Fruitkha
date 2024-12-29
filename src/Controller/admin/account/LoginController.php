<?php

namespace App\Controller\admin\account;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/admin/login', name: 'admin_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
         // Si l'utilisateur est déjà connecté, on le redirige vers le tableau de bord
        if ($this->getUser()) {
            return $this->redirectToRoute('admin_dasboard');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin/loginAdmin.html.twig', [
            'controller_name' => 'LoginController',
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/admin/deconnexion', name: 'admin_logout')]
    public function logout()
    {
        throw new \Exception('This should never be reached!');
    }
}
