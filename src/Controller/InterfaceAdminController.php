<?php

namespace App\Controller;

use App\Entity\Offre;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InterfaceAdminController extends AbstractController
{
    #[Route('/interface/admin', name: 'app_interface_admin')]
    public function index(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $offres = $entityManager->getRepository(Offre::class)->findAll();

        return $this->render('interface_admin/AccepterRefuseroffre.html.twig', [
            'offres' => $offres,
        ]);
    }

    #[Route('/interface/admin/accepter/{id}', name: 'accepter_offre')]
    public function accepterOffre($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $offre = $entityManager->getRepository(Offre::class)->find($id);

        if (!$offre) {
            throw $this->createNotFoundException('Aucune offre trouvée pour cet identifiant : ' . $id);
        }

        $offre->setEtatOffre('acceptée');
        $entityManager->flush();

        return $this->redirectToRoute('app_interface_admin');
    }

    #[Route('/interface/admin/refuser/{id}', name: 'refuser_offre')]
    public function refuserOffre($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $offre = $entityManager->getRepository(Offre::class)->find($id);

        if (!$offre) {
            throw $this->createNotFoundException('Aucune offre trouvée pour cet identifiant : ' . $id);
        }

        $offre->setEtatOffre('refusée');
        $entityManager->flush();

        return $this->redirectToRoute('app_interface_admin');
    }
}
