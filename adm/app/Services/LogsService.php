<?php

namespace App\Services;

use App\Models\LogsModel;

class LogsService{
    protected LogsModel $logs;
    public function __construct(){
        $this->logs = new LogsModel;
    }

    public function inserirLog($acao,$servico,$tabela):void{
        $dadosLog = array(
                'id_usuario' => session('id'),
                'ds_usuario' => session('nome'),
                'ds_acao' => $acao,
                'ds_tabela' => $tabela,
                'ds_mensagem' => $servico,
                'ds_data' => date('Y-m-d'),
                'ds_hora' => date('H:i:s'),
            );

        $this->logs->insert($dadosLog);
    }

}
