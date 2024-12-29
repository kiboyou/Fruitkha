<?php

namespace App\Controller\client\account;

use App\Form\PasswordUserType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AccoutController extends AbstractController
{
    #[Route('/user/compte', name: 'app_account')]
    public function index(CommandeRepository $commandeRepository, Security $security): Response
    {
        //verifie si l'user est connecte
        if(!$security->getUser()){
            return $this->redirectToRoute('user_login');
        }

        $commandes = $commandeRepository->findBy(['client' => $this->getUser()]);
        return $this->render('account/index.html.twig', [
            'commandes' => $commandes
        ]);
    }

    #[Route('/user/compte/modifier_password', name: 'app_account_modifierpassword')]
    public function ModifierPassword(Request $request, Security $security, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        //verifie si l'user est connecte
        if(!$security->getUser()){
            return $this->redirectToRoute('user_login');
        }

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
