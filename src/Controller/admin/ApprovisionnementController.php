<?php

namespace App\Controller\admin;

use App\Entity\Approvisionnement;
use App\Form\ApprovisionnementType;
use App\Repository\ApprovisionnementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/approvisionnement')]
final class ApprovisionnementController extends AbstractController
{
    #[Route(name: 'app_approvisionnement_index', methods: ['GET'])]
    public function index(ApprovisionnementRepository $approvisionnementRepository, Security $security): Response
    {
        if(!$security->getUser()){
                return $this->redirectToRoute('admin_login');
            }
        return $this->render('admin/approvisionnement/index.html.twig', [
            'approvisionnements' => $approvisionnementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_approvisionnement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        if(!$security->getUser()){
            return $this->redirectToRoute('admin_login');
        }
        $approvisionnement = new Approvisionnement();
        $form = $this->createForm(ApprovisionnementType::class, $approvisionnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($approvisionnement);
            $entityManager->flush();

            return $this->redirectToRoute('app_approvisionnement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/approvisionnement/new.html.twig', [
            'approvisionnement' => $approvisionnement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_approvisionnement_show', methods: ['GET'])]
    public function show(Approvisionnement $approvisionnement, Security $security): Response
    {
        if(!$security->getUser()){
            return $this->redirectToRoute('admin_login');
        }
        return $this->render('admin/approvisionnement/show.html.twig', [
            'approvisionnement' => $approvisionnement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_approvisionnement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Approvisionnement $approvisionnement, Security $security, EntityManagerInterface $entityManager): Response
    {
        if(!$security->getUser()){
                return $this->redirectToRoute('admin_login');
            }
        $form = $this->createForm(ApprovisionnementType::class, $approvisionnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_approvisionnement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/approvisionnement/edit.html.twig', [
            'approvisionnement' => $approvisionnement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_approvisionnement_delete', methods: ['POST'])]
    public function delete(Request $request, Security $security, Approvisionnement $approvisionnement, EntityManagerInterface $entityManager): Response
    {
        if(!$security->getUser()){
                return $this->redirectToRoute('admin_login');
            }
        if ($this->isCsrfTokenValid('delete'.$approvisionnement->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($approvisionnement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_approvisionnement_index', [], Response::HTTP_SEE_OTHER);
    }
}
