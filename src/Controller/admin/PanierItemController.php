<?php

namespace App\Controller\admin;

use App\Entity\PanierItem;
use App\Form\PanierItemType;
use App\Repository\PanierItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/panier/item')]
final class PanierItemController extends AbstractController
{
    #[Route(name: 'app_panier_item_index', methods: ['GET'])]
    public function index(PanierItemRepository $panierItemRepository): Response
    {
        return $this->render('admin/panier_item/index.html.twig', [
            'panier_items' => $panierItemRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_panier_item_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $panierItem = new PanierItem();
        $form = $this->createForm(PanierItemType::class, $panierItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($panierItem);
            $entityManager->flush();

            return $this->redirectToRoute('app_panier_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/panier_item/new.html.twig', [
            'panier_item' => $panierItem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_panier_item_show', methods: ['GET'])]
    public function show(PanierItem $panierItem): Response
    {
        return $this->render('admin/panier_item/show.html.twig', [
            'panier_item' => $panierItem,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_panier_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PanierItem $panierItem, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PanierItemType::class, $panierItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_panier_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/panier_item/edit.html.twig', [
            'panier_item' => $panierItem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_panier_item_delete', methods: ['POST'])]
    public function delete(Request $request, PanierItem $panierItem, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$panierItem->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($panierItem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_panier_item_index', [], Response::HTTP_SEE_OTHER);
    }
}
