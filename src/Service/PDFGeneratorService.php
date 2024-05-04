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
        $pdf->SetTitle('Products Purchase');
        $pdf->SetSubject('Products Purchase');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // Ajout de la première page
        $pdf->AddPage();

        // Ajout du logo en haut à droite de la page
        $image_file = 'C:\xampp\htdocs\FlexFlowWeb\public\uploads\logo.jpg'; // Chemin vers le fichier logo
        $pdf->Image($image_file, 175, 11, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

        // Date de facture
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetXY(10, 10);
        $pdf->Cell(0, 10, 'Date: ' . date('Y-m-d H:i:s'), 0, 1, 'L');

        // Ajouter une ligne noire
        $pdf->SetLineWidth(0.5);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->Line(10, 30, 200, 30);

        // Générer un numéro de facture aléatoire avec 5 chiffres
$numFacture = rand(10000, 99999);

// Titre "Facture" au centre avec le numéro de facture
$pdf->SetFont('helvetica', 'B', 16);
$pdf->SetXY(0, 40);
$pdf->Cell(210, 10, 'Facture N° ' . $numFacture, 0, 1, 'C');


        // Header du tableau
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetFillColor(173, 216, 230); // Couleur bleue pour le header
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(0);
        $pdf->SetLineWidth(0.3);
        $pdf->SetFont('', 'B');
        
        // Largeurs des colonnes
        $col_width = 64; // Largeur des colonnes
        $pdf->Cell($col_width, 10, 'Produit', 1, 0, 'C', 1);
        $pdf->Cell($col_width, 10, 'Quantité', 1, 0, 'C', 1);
        $pdf->Cell($col_width, 10, 'Montant', 1, 1, 'C', 1);

        // Contenu du tableau
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetTextColor(0);
        foreach ($products as $product) {
            $pdf->Cell($col_width, 10, $product['nom'], 'LR', 0, 'C');
            $pdf->Cell($col_width, 10, $product['quantite'], 'LR', 0, 'C');
            $pdf->Cell($col_width, 10, $product['montant'], 'LR', 1, 'C');
        }
        $pdf->Cell($col_width * 3, 0, '', 'T'); // Fermer le tableau

        // Ajout de l'image de la signature à droite
        $image_signature = 'C:\xampp\htdocs\FlexFlowWeb\public\uploads\signature.jpg'; // Chemin vers le fichier de signature
        $pdf->Image($image_signature, 150, $pdf->getY() + 30, 40, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

        // Retourner le contenu du PDF en tant que chaîne de caractères
        return $pdf->Output('', 'S');
    }
}
