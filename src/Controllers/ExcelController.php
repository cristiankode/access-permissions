<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Lead;
use App\Mail\EmailNewsletter;

class ExcelController 
{
    public function generateFile()
    {
        $document = new Spreadsheet();
        $document
        ->getProperties()
        ->setCreator("Cristiah Hurtado")
        ->setLastModifiedBy("APE")
        ->setTitle("Archivo generado desde servidores APE")
        ->setDescription("Listado de prospectos suscritos al boletin informativo.");

        $sheet = $document->getActiveSheet();
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->setTitle("Prospectos Plazas");

        $headers = ["Correo", 'Plaza', 'Fecha'];

        $sheet->fromArray($headers, 'A1');
  
        $date_now = date('Y-m-d');
        $date_past = strtotime('-7 day', strtotime($date_now));
        $date_past = date('Y-m-d', $date_past);

        $leads = new Lead();
        $result = $leads->findWhereDate( $date_past, $date_now );

        $path = $_SERVER['PATH_DOCUMENT'] . "/files/leads.xlsx";
        if($result->fetchObject()) {
            $row = 2;
            while($lead = $result->fetchObject()) {
                $sheet->setCellValueByColumnAndRow(1, $row, $lead->correo);
                $sheet->setCellValueByColumnAndRow(2, $row, $lead->plaza);
                $sheet->setCellValueByColumnAndRow(3, $row, $lead->fecha);
                $row++;
            }
            
            echo "Enviando archivo fechas: " .  $date_past . " al " . $date_now;
            $writer = new Xlsx($document);
            $writer->save($path);
            
            $email = new EmailNewsletter($_SERVER['MAIL_USERNAME'], explode(',', $_SERVER['MAIL_TO']), explode(',', $_SERVER['MAIL_BCC']), "Suscripciones semanales {$date_past} - {$date_now}");
            $email->send();
        } else {
            echo "No se encontraron suscriptores";
            if(file_exists($path)) {
                unlink($path);
            }
        }
    }
}