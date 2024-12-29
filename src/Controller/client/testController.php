<?php

declare(strict_types=1);

namespace App\Controller\client;

use App\Repository\FruitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class testController extends AbstractController
{
    #[Route('/user/test', name: 'app_test_index', methods: ['GET'])]
    public function index(FruitsRepository $fruitsRepository): Response
    {
        return $this->render('test/index.html.twig',[
            'fruits' => $fruitsRepository->findAll(),
        ]);
    }
}
