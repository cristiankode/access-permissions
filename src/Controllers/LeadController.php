<?php

namespace App\Controllers;

use App\Models\Lead;
use App\Dtos\LeadDto;
use DateTime;

class LeadController
{
    public function store(array $request)
    {
        if( filter_var($request['email'], FILTER_VALIDATE_EMAIL) ) {
            
            $leadDto = new LeadDto($request['email'], $request['state_code_id']);
            date_default_timezone_set('America/Mexico_City');

            $date = new DateTime();
            $leadDto->setCreatedAt( $date->format('Y-m-d H:i:s') );
        
            $lead = new Lead();
            if($lead->store($leadDto)) {
                http_response_code(201); 
                echo json_encode(array("message" => "Item was created."));
            } else {
                http_response_code(503); 
                echo json_encode( array("message" => "Unable to create item.") );
            }
        } else {
            http_response_code(503);
            echo json_encode( array("message" => "Dirección de correo no es válida.") );
        }
    }
}