<?php
namespace App\Controller;

use App\Entity\Evaluations; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class EvaluationController extends AbstractController
{
    #[Route('/evaluation', name: 'app_evaluation')]
    public function traiterEvaluation(Request $request): Response
    {
        // Récupérer les données de la requête
        $offreId = $request->request->get('id');
        $note = $request->request->get('note '); // Le champ rating correspond à la note
    
        // Assurez-vous que les données nécessaires sont présentes
        if (!$offreId || !$note) {
            return new Response('Note bien envoyeé', Response::HTTP_BAD_REQUEST);
        }
    
        // Enregistrez l'évaluation dans la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $evaluation = new Evaluations();
        $evaluation->setOffreId($offreId);
        $evaluation->setNote($note);
        $entityManager->persist($evaluation);
        $entityManager->flush();
    
        // Si l'évaluation est enregistrée avec succès, vous pouvez renvoyer une réponse JSON
        // ou rediriger l'utilisateur vers une autre page
        return $this->json(['message' => 'Évaluation enregistrée avec succès'], Response::HTTP_CREATED);
    }
    
}







