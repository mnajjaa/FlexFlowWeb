<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IMCController extends AbstractController
{
    #[Route('/i/m/c', name: 'app_i_m_c')]
    public function index(): Response
    {
        return $this->render('imc/index.html.twig', [
            'controller_name' => 'IMCController',
        ]);
    }
}
