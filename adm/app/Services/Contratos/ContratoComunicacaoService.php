<?php
/*
Quem usa:
Control: Contratos.php
*/
namespace App\Services\Contratos;

use App\Models\DadosContratosModel;
use App\Models\CandidatosModel;
use App\Models\SetoresModel;
use App\Models\ComunicacoesModel;

use App\Services\ContagemDeTempoService;
use App\Services\LogsService;
use App\Services\Base\AbstractCrudService;
use App\Services\Base\AbstractEmailService;


class ContratoComunicacaoService extends AbstractCrudService{
    public function __construct(
        protected AbstractEmailService $emailService,
        protected LogsService $logsService, 
        protected ContagemdeTempoService $contagemdeTempoService
    ){
        parent::__construct();
        $this->emailService = $emailService;
        $this->logsService  = $logsService;
    }

    public function comunicarContratoExpirando(int $idContrato, array $dadosComunicacao): bool{
        return $this->transactional(function () use ($idContrato, $dadosComunicacao){
            //Buscar o contatro
            $contratosModel    = new DadosContratosModel();
            $contrato = $contratosModel->find($idContrato);

            if(!$contrato){
                throw new \RuntimeException('Contrato nao encontrado');
            }

            //Buscar o id do candidato e o id do setor
            $candidato = (new CandidatosModel())->find($contrato->fk_id_candidato);
            $setor = (new SetoresModel())->find($contrato->fk_id_setor);

            //FAzer a contagem de tempo
            $contagemService = new ContagemDeTempoService();
            $contagem = $contagemService->contagemDeTempoContrato($contrato->fk_id_candidato, $contrato->fk_id_setor, $contrato->pk_id_contrato);

            //montar a mensagem e mandar pra /views/emails/contrato_expirando
            $mensagem = view('emails/contrato_expirando', [
                'nome' => $candidato->ds_nome,
                'setor' => $setor->ds_nome_setor,
                'dataFim' => $contrato->ds_data_encerramento,
                'diasTrabalhados' => $contagem['diasTrabalhados'],
                'diasFerias' => $contagem['diasFerias'],
                'feriasTiradas' => $contagem['feriasTiradas'],
            ]);
            //Atualizar a tablea de contratos
            $contratosModel->update($idContrato,['ds_notificado' => 1]);

            //Inserir dados na tabela de comunicacoes
            (new ComunicacoesModel())->insert([
                'ds_assunto'=> 'Comunicação de contrato expirando',
                'ds_destinatario'=> $candidato->ds_nome,
                'ds_email'=> $candidato->ds_email,
                'ds_data' => date('Y-m-d'),
                'ds_hora' => date('H:i:s'),

            ]);

            //enviar o email
            return $this->emailService->enviar([
            'email'=>$candidato->ds_email,
            'nome'=>$candidato->ds_nome,
            'assunto'=>'Comunicação de contrato expirando',
            'mensagem'=>$mensagem
        ]);
            //gerar log
            $this->logsService->inserirLog('Comunicou','Comunicou ao candidato '.$candidato->pk_id_candidato.'-'.$candidato->ds_nome.' o contrato expirando. Contrato ID '.$idContrato);
        });
        
        return true;

    }    

}