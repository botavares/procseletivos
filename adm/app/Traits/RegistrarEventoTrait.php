<?php
namespace App\Traits;
trait registrarEventoTrait{

public function registrarEvento($userId=NULL,$acao=NULL,$idManifesto=NULL,$protocolo=NULL){
        $evento = new \App\Models\LogsModel();
        $dadosEvento = array(
            'ds_data'                  => date('Y-m-d'),
            'ds_hora'                  => date('H:i:s'),
            'fk_id_user'                => $userId,
            'ds_acao'                  => $acao,
            'ds_servico'           => $idManifesto,
            
        );
        if($evento->insert($dadosEvento)){
            return true;
        }
   }
}