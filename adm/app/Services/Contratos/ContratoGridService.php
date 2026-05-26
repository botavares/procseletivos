<?php

namespace App\Services\Contratos;

use App\Models\DadosContratosModel;
use App\Services\Base\AbstractGridService;
use App\Services\ContagemDeTempoService;

class ContratoGridService extends AbstractGridService{
    protected ContagemDeTempoService $contagemService;

    public function __construct(){
        parent::__construct(new DadosContratosModel());

        $this->contagemService = new ContagemDeTempoService();
    }

    public function ativos(): array{

    //gerar titulo de cada coluna (ativo é diferente de expirando por isso está fora do constructor)
        $this->setColumns([
            'Nome',
            'Curso',
            'Setor',
            'Data Encerramento',
            'Dias Trabalhados',
            'Férias Disponíveis',
            'Férias Tiradas',
           
        ])
        ->setOrder('ds_data_inicial', 'asc');

        $contratos = $this->model->getContratosAtivos();

        $dados = [];

        foreach ($contratos as $contrato) {

            $contagem = $this->contagemService->contagemDeTempoContrato(
                $contrato->fk_id_candidato,
                $contrato->fk_id_setor,
                $contrato->pk_id_contrato
            );

            $dados[] = [
                'pk_id_contrato'        => $contrato->pk_id_contrato,
                'fk_id_candidato'       => $contrato->fk_id_candidato,
                'ds_nome_candidato'     => $contrato->ds_nome,
                'ds_numero_edital'      => $contrato->ds_numero_edital,
                'ds_nome_curso'         => $contrato->ds_nome_curso,
                'ds_nome_setor'         => $contrato->ds_nome_setor,
                'ds_data_ingresso'      => $contrato->ds_data_ingresso,
                'ds_data_encerramento'  => $contrato->ds_data_encerramento,
                'ds_notificado'         => $contrato->ds_notificado,
                'diasTrabalhados'       => $contagem['diasTrabalhados'],
                'feriasDisponiveis'     => $contagem['diasFerias'],
                'feriasTiradas'         => $contagem['feriasTiradas'],
                'ds_status'             => $contrato->ds_status,
            ];
        }

        return [
            'dados'             => $dados,
            'columns'           => $this->columns,
            'contratosExpirando' => $this->model->getContratosExpirando(32),
        ];
    }

    public function expirando(int $dias): array{
        $this->setColumns([
            'Nome',
            'Curso',
            'Lotação',
            'e_Mail',
            'Telefone',
            'Início do Contrato',
            'Encerramento do Contrato',
        ])
        ->setOrder('ds_data_inicial', 'asc');
  
        $expirando = $this->model->getContratosExpirando($dias);

        $dados = [];
        foreach ($expirando as $valueExpirando) {
          $contagem = $this->contagemService->contagemDeTempoContrato(
                $valueExpirando->fk_id_candidato,
                $valueExpirando->fk_id_setor,
                $valueExpirando->pk_id_contrato
            );


        $dados[] = [
                'pk_id_contrato'        => $valueExpirando->pk_id_contrato,
                'fk_id_candidato'       => $valueExpirando->fk_id_candidato,
                'ds_nome_candidato'     => $valueExpirando->ds_nome,
                'ds_nome_curso'         => $valueExpirando->ds_nome_curso,
                'ds_nome_setor'         => $valueExpirando->ds_nome_setor,
                'ds_email'              => $valueExpirando->ds_email,
                'ds_data_ingresso'      => $valueExpirando->ds_data_ingresso,
                'ds_data_encerramento'  => $valueExpirando->ds_data_encerramento,
                'ds_notificado'         => $valueExpirando->ds_notificado,
                
            ];

        }

        return [
            'dados'    => $dados,
            'columns' => $this->columns,
            'contratosExpirando' => $this->model->getContratosExpirando(32)
        ];
    }
}
