<?php
// src/Service/PDFGeneratorService.php

namespace App\Service;

use TCPDF;

class PDFGeneratorService
{
    public function generatePDF(array $products): string
    {
        // Initialisation de TCPDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Paramètres du document
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Facture d\'Achat');
        $pdf->SetSubject('Facture d\'Achat');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // Ajout de la première page
        $pdf->AddPage();

        // Ajout du logo en haut à droite de la page
        $image_file = 'C:\xampp\htdocs\FlexFlowWeb\public\uploads\logo.jpg'; // Chemin vers le fichier logo
        $pdf->Image($image_file, 175, 13, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

        // Date de facture
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetXY(10, 10);
        $pdf->Cell(0, 10, 'Date: ' . date('Y-m-d H:i:s'), 0, 1, 'L');

        // Ajout d'informations complémentaires
        $pdf->SetFont('helvetica', '', 10);
        
        $pdf->Cell(0, 4, '', 0, 1, 'L');
        $pdf->Cell(0, 7, 'Code Postal: 2046', 0, 1, 'L');
        $pdf->Cell(0, 7, 'Pays: Tunisie', 0, 1, 'L');
        $pdf->Cell(0, 7, 'Adresse: Tunis, Ghazela', 0, 1, 'L');
        $pdf->Cell(0, 7, 'Email: flexflow@gmail.com', 0, 1, 'L');
        $pdf->Cell(0, 7, 'Téléphone: +216 29678126', 0, 1, 'L');

        // Ajout une ligne de séparation
        $pdf->Ln(10);

        // Titre "Facture" au centre avec le numéro de facture
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(210, 10, 'Facture N° ' . rand(10000, 99999), 0, 1, 'C');

        // Header du tableau
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetFillColor(92, 184, 209); // Nouvelle couleur bleue pour le header
        $pdf->SetTextColor(0); // Texte en noir
        $pdf->SetDrawColor(0);
        $pdf->SetLineWidth(0.3);
        $pdf->Cell(60, 10, 'Produit', 1, 0, 'C', 1);
        $pdf->Cell(40, 10, 'Quantité', 1, 0, 'C', 1);
        $pdf->Cell(40, 10, 'Prix Unitaire', 1, 0, 'C', 1);
        $pdf->Cell(50, 10, 'Montant', 1, 1, 'C', 1);

        // Contenu du tableau
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetTextColor(0);

        $montantTotal = 0;
        foreach ($products as $product) {
            $pdf->Cell(60, 10, $product['nom'], 'LR', 0, 'C');
            $pdf->Cell(40, 10, $product['quantite'], 'LR', 0, 'C');
            $pdf->Cell(40, 10, '' . number_format($product['prix'], 2), 'LR', 0, 'C');
            $pdf->Cell(50, 10, '' . number_format($product['montant'], 2), 'LR', 1, 'C');

            $montantTotal += $product['montant']; // Calculer le montant total
        }
        $pdf->Cell(190, 0, '', 'T'); // Fermer le tableau

        // Calcul de la TVA
        $tva = $montantTotal * 0.19;
        $montantApresTVA = $montantTotal + $tva;

               // Affichage des informations TVA
               $pdf->SetFont('helvetica', '', 12);
               $pdf->SetXY(10, $pdf->getY() + 10);
               $pdf->Cell(0, 10, 'Montant Total : ' . number_format($montantTotal, 2), 0, 1, 'R');
               $pdf->Cell(0, 10, 'Montant TVA (19%) : ' . number_format($tva, 2), 0, 1, 'R');
               $pdf->Cell(0, 10, 'Montant après TVA : ' . number_format($montantApresTVA, 2), 0, 1, 'R');

        // Ajout du texte "Offre valable jusqu'à" à gauche sous le tableau
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetXY(10, $pdf->getY() + 10);
        $pdf->Cell(0, 10, 'commande valable jusqu\'à : ' . date('Y-m-d', strtotime('+7 days')), 0, 1, 'L');

        // Ajout de l'image de la signature sous le texte "Signature"
        $image_signature = 'C:\xampp\htdocs\FlexFlowWeb\public\uploads\signature.jpg'; // Chemin vers le fichier de signature
        $pdf->Image($image_signature, 150, $pdf->getY() + 20, 40, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

        // Ajout du texte "Signature" au-dessus de l'image de la signature
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetXY(150, $pdf->getY() - 9);
        $pdf->Cell(40, 10, 'Signature', 0, 1, 'C');

        // Retourner le contenu du PDF en tant que chaîne de caractères
        return $pdf->Output('', 'S');
    }
}
