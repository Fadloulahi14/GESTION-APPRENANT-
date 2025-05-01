<?php
require_once __DIR__ . '/../../vendor/autoload.php'; // Assurez-vous que PhpSpreadsheet et FPDF sont inclus

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


function exportToCSV($apprenants, $referenciels) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="liste_apprenants.csv"');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // Ajout du BOM UTF-8 pour Excel

    fputcsv($output, ['Matricule', 'Nom Complet', 'Adresse', 'Téléphone', 'Email', 'Référentiel', 'Statut']);

    foreach ($apprenants as $apprenant) {
        fputcsv($output, [
            $apprenant['matricule'],
            $apprenant['prenom'] . ' ' . $apprenant['nom'],
            $apprenant['adresse'],
            $apprenant['telephone'],
            $apprenant['email'],
            $referenciels[$apprenant['referenciel']]['nom'] ?? 'N/A',
            $apprenant['statut']
        ]);
    }

    fclose($output);
    exit;
}

function exportToPDF($apprenants, $referenciels) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);

    $pdf->Cell(0, 10, 'Liste des Apprenants', 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(30, 10, 'Matricule', 1);
    $pdf->Cell(50, 10, 'Nom Complet', 1);
    $pdf->Cell(40, 10, 'Adresse', 1);
    $pdf->Cell(30, 10, 'Téléphone', 1);
    $pdf->Cell(40, 10, 'Référentiel', 1);
    $pdf->Cell(20, 10, 'Statut', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    foreach ($apprenants as $apprenant) {
        $matricule = $apprenant['matricule'] ?? 'N/A';
        $prenom = $apprenant['prenom'] ?? 'N/A';
        $nom = $apprenant['nom'] ?? 'N/A';
        $adresse = $apprenant['adresse'] ?? 'N/A';
        $telephone = $apprenant['telephone'] ?? 'N/A';
        $referencielId = $apprenant['referenciel'] ?? null;
        $referencielNom = $referenciels[$referencielId]['nom'] ?? 'N/A';
        $statut = $apprenant['statut'] ?? 'N/A';

        $pdf->Cell(30, 10, $matricule, 1);
        $pdf->Cell(50, 10, $prenom . ' ' . $nom, 1);
        $pdf->Cell(40, 10, $adresse, 1);
        $pdf->Cell(30, 10, $telephone, 1);
        $pdf->Cell(40, 10, $referencielNom, 1);
        $pdf->Cell(20, 10, $statut, 1);
        $pdf->Ln();
    }

    // Nettoyer le tampon de sortie avant de générer le PDF
    ob_clean();
    $pdf->Output('D', 'liste_apprenants.pdf');
    exit;
}

function exportToExcel($apprenants, $referenciels) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'Matricule');
    $sheet->setCellValue('B1', 'Nom Complet');
    $sheet->setCellValue('C1', 'Adresse');
    $sheet->setCellValue('D1', 'Téléphone');
    $sheet->setCellValue('E1', 'Email');
    $sheet->setCellValue('F1', 'Référentiel');
    $sheet->setCellValue('G1', 'Statut');
    

    $row = 2;
    foreach ($apprenants as $apprenant) {
        $matricule = $apprenant['matricule'] ?? 'N/A';
        $prenom = $apprenant['prenom'] ?? 'N/A';
        $nom = $apprenant['nom'] ?? 'N/A';
        $adresse = $apprenant['adresse'] ?? 'N/A';
        $telephone = $apprenant['telephone'] ?? 'N/A';
        $email = $apprenant['email'] ?? 'N/A';
        $referencielNom = $referenciels[$apprenant['referenciel']]['nom'] ?? 'N/A';
        $statut = $apprenant['statut'] ?? 'N/A';

        $sheet->setCellValue('A' . $row, $matricule);
        $sheet->setCellValue('B' . $row, $prenom . ' ' . $nom);
        $sheet->setCellValue('C' . $row, $adresse);
        $sheet->setCellValue('D' . $row, $telephone);
        $sheet->setCellValue('E' . $row, $email);
        $sheet->setCellValue('F' . $row, $referencielNom);
        $sheet->setCellValue('G' . $row, $statut);
        $row++;
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="liste_apprenants.xlsx"');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}