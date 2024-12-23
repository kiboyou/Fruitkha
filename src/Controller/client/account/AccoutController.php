<?php

namespace App\Controller\client\account;

use App\Form\PasswordUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AccoutController extends AbstractController
{
    #[Route('/compte', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }

    #[Route('/compte/modifier_password', name: 'app_account_modifierpassword')]
    public function ModifierPassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser();

         $form = $this->createForm(PasswordUserType::class, $user, [
            'PasswordHasher' => $passwordHasher // On passe le hasher comme une option du formulaire
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Votre mot de passe a bien été modifié');
        }

        return $this->render('account/password.html.twig'
            , ['passwordForm' => $form->createView()]);
    }
}
