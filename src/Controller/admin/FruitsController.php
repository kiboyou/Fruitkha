<?php

namespace App\Controller\admin;

use App\Entity\Fruits;
use App\Form\FruitsType;
use App\Repository\FruitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/fruits')]
final class FruitsController extends AbstractController
{
    #[Route(name: 'app_fruits_index', methods: ['GET'])]
    public function index(FruitsRepository $fruitsRepository): Response
    {
        return $this->render('admin/fruits/index.html.twig', [
            'fruits' => $fruitsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_fruits_new', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $fruit = new Fruits();
        $form = $this->createForm(FruitsType::class, $fruit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'illustration (upload d'image)
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form->get('illustration')->getData();

            if ($file) {
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('fruit_illustrations_directory'),
                    $filename
                );
                $fruit->setIllustration($filename);
            }

            $entityManager->persist($fruit);
            $entityManager->flush();

            return $this->redirectToRoute('app_fruits_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/fruits/new.html.twig', [
            'fruit' => $fruit,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_fruits_show', methods: ['GET'])]
    public function show(Fruits $fruit): Response
    {
        return $this->render('admin/fruits/show.html.twig', [
            'fruit' => $fruit,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_fruits_edit', methods: ['GET', 'POST'])]
    public function update(Request $request, Fruits $fruit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FruitsType::class, $fruit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'illustration (upload d'image)
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form->get('illustration')->getData();

            if ($file) {
                // Supprimer l'ancienne illustration si elle existe
                if ($fruit->getIllustration()) {
                    $oldFilePath = $this->getParameter('fruit_illustrations_directory') . '/' . $fruit->getIllustration();
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath); // Supprime l'ancienne image
                    }
                }

                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('fruit_illustrations_directory'),
                    $filename
                );
                $fruit->setIllustration($filename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_fruits_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/fruits/edit.html.twig', [
            'fruit' => $fruit,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_fruits_delete', methods: ['POST'])]
    public function delete(Request $request, Fruits $fruit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fruit->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($fruit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_fruits_index', [], Response::HTTP_SEE_OTHER);
    }
}
