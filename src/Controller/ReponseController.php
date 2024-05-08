<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Form\ReponseType;
use App\Repository\ReponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use App\Entity\Reclamation;

#[Route('/reponse')]
class ReponseController extends AbstractController
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }



    #[Route('/addReclamation', name: 'app_reponse_index', methods: ['GET'])]
    public function index(ReponseRepository $reponseRepository): Response
    {
        return $this->render('reponse/index.html.twig', [
            'reponses' => $reponseRepository->findAll(),
        ]);
    }



    #[Route('/new/{reclamation_id}', name: 'app_reponse_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,$reclamation_id): Response
    {
        // Récupérer la réclamation correspondante à partir de l'ID
        $reclamation = $this->getDoctrine()
        ->getRepository(Reclamation::class)
        ->find($reclamation_id);



        $reponse = new Reponse();

        $reponse->setReclamation($reclamation);
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reponse);
            $entityManager->flush();


// Récupère l'adresse e-mail de destination depuis le fichier .env
$destinationEmail = 'maalejahmed55@gmail.com';

// Envoie de l'e-mail
$email = (new Email())
    ->from('FlexFlow<expediteur@example.com>') // Adresse e-mail de l'expéditeur
    ->to($destinationEmail)
    ->subject('Nouvelle réponse ajoutée')
    //->text('Une nouvelle réponse a été ajoutée.');
    ->html('
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nouvelle réponse ajoutée</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                color: #333;
                background-color: #f4f4f4;
                margin: 0;
                padding: 20px;
            }
            .container {
                background-color: #ffffff;
                border: 1px solid #dddddd;
                padding: 20px;
                max-width: 600px;
                margin: auto;
            }
            .header {
                background-color: #673ab7;
                color: #ffffff;
                padding: 10px 20px;
                text-align: center;
            }
            .footer {
                background-color: #eeeeee;
                color: #333;
                text-align: center;
                padding: 10px 20px;
                font-size: 12px;
            }
            .content {
                margin-top: 20px;
            }
            .logo {
                display: block;
                margin: auto;
                padding-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Nouvelle réponse ajoutée</h1>
            </div>
    
            <!-- Logo -->
            <img src="cid:logo"  class="logo">
            
    
            <!-- Contenu principal -->
            <div class="content">
                <p>Cher utilisateur,</p>
                <p>Une nouvelle réponse a été ajoutée à votre requête.</p>
            </div>
    
            <!-- Pied de page -->
            <div class="footer">
                <p>Contactez-nous :</p>
                
                <p>Tél: + 216 55224477</p>
                <p>Adresse: 01 - Rue Palestine - Chaguia 2</p>
            </div>
        </div>
    </body>
    </html>'
    

    );
    $email->embed(fopen('C:\xampp\htdocs\FlexFlowWeb\public\logo.png', 'r'), 'logo');




    $this->mailer->send($email);


            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reponse/new.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_show', methods: ['GET'])]
    public function show(Reponse $reponse): Response
    {
        return $this->render('reponse/show.html.twig', [
            'reponse' => $reponse,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reponse_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reponse_index_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reponse/edit.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_delete', methods: ['POST'])]
    public function delete(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reponse->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reponse);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reponse_index_admin', [], Response::HTTP_SEE_OTHER);
    }

    
#route reponse_admin#

    #[Route('/reponse_admin', name: 'app_reponse_index_admin', methods: ['GET'])]
    public function indexAdmin(ReponseRepository $reponseRepository): Response
    {
        return $this->render('reponse/index-admin.html.twig', [
            'reponses' => $reponseRepository->findAll(),
        ]);
    }
    
  
}
