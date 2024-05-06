<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Evenement;
use App\Entity\Favoris;

class FavorisController extends AbstractController
{
    #[Route('/like/{id}', name: 'like')]
    public function like(Evenement $evenement): Response
    {
        $favoris = $this->getDoctrine()->getRepository(Favoris::class)->findOneBy([
            'user' => $this->getUser(),
            'evenement' => $evenement,
        ]);
    
        if (!$favoris) {
            $favoris = new Favoris();
            $favoris->setUser($this->getUser());
            $favoris->setEvenement($evenement);
        }
    
        $favoris->setLoved(true);
        $favoris->setUnloved(false);
    
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($favoris);
        $entityManager->flush();
    
        return $this->json(['status' => 'liked']);
    }

    #[Route('/dislike/{id}', name: 'dislike')]
    public function dislike(Evenement $evenement): Response
    {
        $favoris = $this->getDoctrine()->getRepository(Favoris::class)->findOneBy([
            'user' => $this->getUser(),
            'evenement' => $evenement,
        ]);
    
        if ($favoris) {
            $favoris->setLoved(false);
            $favoris->setUnloved(true);
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($favoris);
            $entityManager->flush();
        }
    
        return $this->json(['status' => 'disliked']);
    }
    #[Route('/status/{id}', name: 'status')]

    public function status(Evenement $evenement): Response
    {
        $favoris = $this->getDoctrine()->getRepository(Favoris::class)->findOneBy([
            'user' => $this->getUser(),
            'evenement' => $evenement,
        ]);
    
        $status = 'none';
        if ($favoris) {
            $status = $favoris->isLoved() ? 'liked' : 'disliked';
        }
    
        return $this->json(['status' => $status]);
    }
    
}
