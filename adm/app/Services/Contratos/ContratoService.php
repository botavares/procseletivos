<?php
/*Quem usa:
Control: Contratos.php
Control: Dashboard.php
*/
namespace App\Services\Contratos;
use App\Services\Base\AbstractCrudService;
use App\Models\DadosContratosModel;
use App\Models\ConvocadosModel;
use App\Models\VagasModel;
use App\Models\VerificadorModel;
use App\Models\DadosRescisaoModel;
use App\Models\DadosAditivosModel;
use App\Services\LogsService;

class ContratoService extends AbstractCrudService
{
    public function __construct(
        protected DadosContratosModel $contratosModel,
        protected VagasModel $vagasModel,
        protected LogsService $logsService,
        protected DadosRescisaoModel $rescisaoModel,
        protected DadosAditivosModel $aditivosModel,
        protected ConvocadosModel $convocadosModel,
        protected VerificadorModel $verificadorModel,
    ) {
        parent::__construct();
    }

    public function contratar(array $dados,): int
    {
        return $this->transactional(function () use ($dados) {

            //verificar se o candidato possui contrato ativo
            $contratoAtivo = $this->contratosModel
                ->where('fk_id_candidato', $dados['fk_id_candidato'])
                ->where('ds_status', '1')
                ->first();

            if ($contratoAtivo) {
                throw new \RuntimeException('O candidato possui contrato ativo');
            }
            $this->contratosModel->insert($dados);

            if (!$this->contratosModel->affectedRows()) {
                throw new \RuntimeException('Falha ao criar contrato');
            }

            $idContrato = $this->contratosModel->getInsertID();

            $this->vagasModel->debitarVaga(
                $dados['fk_id_setor'],
                $dados['fk_id_curso']
            );

            //gerar codigo verificador
            $codigoVerificacao = sendMeAuth();
            $dadosVerificar = array(
                'pk_id_contrato' => $idContrato,
                'ds_verificador'   => $codigoVerificacao,
            );            
            $this->verificadorModel->insert($dadosVerificar);

            
            $dadosAtualizar = [
                'ds_comparecimento' => '1',
                'ds_interesse' => '1',
                'ds_status' => '2'
            ];
            //atualizar o status de comparecimento para 1 (caso esteja 0)
            $this->convocadosModel->where('fk_id_candidato', $dados['fk_id_candidato'])->set($dadosAtualizar)->update();

            $this->logsService->inserirLog(
                'Adicionou',
                'Gerou o Contrato ID '.$idContrato
            );

            return $idContrato;
        });
    }

   

    public function alterar(array $dados): void{
        $this->transactional(function () use ($dados) {
            $idContrato = $dados['pk_id_contrato'];
            $idCursoAntigo = $dados['fk_id_cursoAntigo'];
            $idCursoAtual = $dados['fk_id_curso'];
            $idSetorAntigo = $dados['fk_id_setorAntigo'];
            $idSetorAtual = $dados['fk_id_setor'];

            
            unset($dados['pk_id_contrato']);
            $this->contratosModel->where('pk_id_contrato', $idContrato)->update(null,$dados);

            if($idCursoAntigo === $idCursoAtual && $idSetorAntigo === $idSetorAtual){
                return;
            }else{
                $this->vagasModel->creditarVaga($idSetorAntigo, $idCursoAntigo);
                $this->vagasModel->debitarVaga($idSetorAtual, $idCursoAtual);
            }

            if(!$this->contratosModel->affectedRows()){
                throw new \RuntimeException('Nenhuma alteração foi realizada no contrato');
            }

            $this->logsService->inserirLog(
                'Alterou',
                "Contrato ID {$idContrato} foi alterado"
            );
        });
    }

    public function rescindir(array $dados): bool{
        return $this->transactional(function () use ($dados) {
            $modo = $dados['modo'];
            $idContrato = (int) $dados['idContrato'];
          
            //verificar se esse contrato possui rescisão
            $jaPossuiRescisao = $this->rescisaoModel->where('fk_id_contrato', $idContrato)->first();
            if($jaPossuiRescisao){
                throw new \DomainException('Contrato já possui rescisão.');
            }
            //preparar array rescisao
            $arrayRescisao = [
                'fk_id_contrato' => $idContrato,
                'fk_id_candidato' => $dados['idCandidato'],
                'ds_data_baixa' => $dados['dataBaixa'],
                'fk_id_motivo'  =>$dados['motivo']
            ];
            if($this->rescisaoModel->insert($arrayRescisao)){
                if (!$this->rescisaoModel->affectedRows()) {
                    throw new \RuntimeException('Falha ao inserir aditivo');
                }
            //preparar array para atualizar o status do contrato
                $arrayContrato = [
                    'ds_status' => '2',
                    'ds_data_baixa'=>$dados['dataBaixa'],
                ];
                $this->contratosModel->update($idContrato, $arrayContrato);
            
                //preparar array para atualizar o status do convocado (caso esteja 1)
                $arrayConvocacao = [
                    'ds_status' => '3',
                ];
                $this->convocadosModel
                    ->where('fk_id_candidato', $dados['idCandidato'])
                    ->where('ds_status', '2') //status dois na tabela de convocados significa 'contratado'
                    ->set($arrayConvocacao)->update();
                //preparar array para atualizar o status de aditivos
                $arrayAditivo = [
                    'ds_status' => '2',
                ]
                ;
                $this->aditivosModel->where('fk_id_contrato', $idContrato)->set($arrayAditivo)->update();
                
                $this->logsService->inserirLog(
                        'Rescindiu',
                        "Contrato ID {$idContrato} foi rescindido"
                    );
                    return true;
                }
            
        });
    }
    public function aditivar(array $dados): bool{
        return $this->transactional(function () use ($dados) {
            $idContrato = (int) $dados['idContrato'];
            
            //Verifica se já possui aditivo
            $jaPossuiAditivo = $this->aditivosModel
                ->where('fk_id_contrato', $idContrato)
                ->orderBy('ds_numero_aditivo', 'desc')
                ->findAll();
            if ($jaPossuiAditivo) {
                //se possui aditivo, encerrar qualquer aditivo que estiver em aberto
                $arrayAditivo = [
                    'ds_status' => '2',
                ]
                ;
                $this->aditivosModel
                    ->where('fk_id_contrato', $idContrato)
                    ->set($arrayAditivo)
                    ->update();
            }
            
            //identificar qual é o maior numero de aditivo relacionado a esse contrato
            $result = $this->aditivosModel
                        ->selectMax('ds_numero_aditivo')
                        ->where('fk_id_contrato', $idContrato)
                        ->first();

                        $numeroAditivo = ((int) $result->ds_numero_aditivo) + 1;


            $arrayAditivo = [
                'fk_id_contrato'       => $idContrato,
                'ds_numero_aditivo'    => $numeroAditivo,
                'ds_ano_aditivo'       => date('Y'),
                'fk_id_setor'          => $dados['fk_id_setor'],
                'ds_supervisor'        => $dados['ds_supervisor'],
                'ds_carga_horaria'     => $dados['ds_carga_horaria'],
                'ds_data_aditivo'      => date('Y-m-d'),
                'ds_data_prorrogacao'  => $dados['ds_data_prorrogacao'],
                'ds_status'            => 1,
            ];

            $this->aditivosModel->insert($arrayAditivo);

            if (!$this->aditivosModel->affectedRows()) {
                throw new \RuntimeException('Falha ao inserir aditivo');
            }

            // Atualiza contrato
            $this->contratosModel->update($idContrato, [
                'ds_prorrogado' => 1,
            ]);

            $this->logsService->inserirLog(
                'Aditivou',
                "Contrato ID {$idContrato} foi aditivado"
            );

            return true;
        });
    }
    public function verificarContratosVencidos(): void
{
    $this->transactional(function () {

        $hoje = date('Y-m-d');

        // CASO 1 — Contratos NÃO prorrogados vencidos
        $naoProrrogados = $this->contratosModel
            ->where('ds_prorrogado', 0)
            ->where('ds_status !=', 2)
            ->where('ds_data_encerramento <', $hoje)
            ->findAll();

        if (!empty($naoProrrogados)) {
            $ids = array_column($naoProrrogados, 'pk_id_contrato');

            $this->contratosModel
                ->whereIn('pk_id_contrato', $ids)
                ->set(['ds_status' => 2])
                ->update();

            $this->logsService->inserirLog(
                'Atualização automática',
                'Contratos não prorrogados encerrados: ' . implode(',', $ids)
            );
        }

        // CASO 2 — Contratos PRORROGADOS sem aditivo válido
        $contratosProrrogados = $this->contratosModel
            ->select('pk_id_contrato')
            ->where('ds_prorrogado', 1)
            ->where('ds_status !=', 2)
            ->findAll();

        foreach ($contratosProrrogados as $contrato) {

            $existeAditivoValido = $this->aditivosModel
                ->where('fk_id_contrato', $contrato->pk_id_contrato)
                ->where('ds_data_prorrogacao >=', $hoje)
                ->first();

            if (!$existeAditivoValido) {

                $this->contratosModel
                    ->update($contrato->pk_id_contrato, [
                        'ds_status' => 2
                    ]);

                $this->logsService->inserirLog(
                    'Atualização automática',
                    "Contrato ID {$contrato->pk_id_contrato} encerrado por expiração de aditivos"
                );
            }
        }
    });
}
   

}
