<?php

declare(strict_types=1);

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/dasboard', name: 'admin_dasboard')]
    public function index(): Response
    {
        return $this->render('admin/pages/dasboard.html.twig');
    }

    #[Route('/admin/users', name: 'admin_users')]
    public function users(): Response
    {
        return $this->render('admin/pages/list-user.html.twig');
    }
}