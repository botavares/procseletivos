<?php
/*
Quem usa:
Control: Contratos.php
*/
namespace App\Services\Contratos;

use App\Models\DadosContratosModel;
use App\Models\CandidatosModel;
use App\Models\SetoresModel;

use CodeIgniter\HTTP\IncomingRequest;

use App\Services\ContagemDeTempoService;

class ContratoComunicacaoFormService {
    protected IncomingRequest $request;
    public function __construct(IncomingRequest $request) {
        $this->request = $request;
    }

    public function prepararFormulario(int $idCandidato, int $setor) {
        $contratosModel    = new DadosContratosModel();
        $candidatosModel   = new CandidatosModel();
        $setoresModel      = new SetoresModel();
        
        $cadosCandidato = $candidatosModel->find($idCandidato);
        $dadosSetor = $setoresModel->find($setor);

        $dadosContrato = $contratosModel
            ->where('fk_id_candidato', $idCandidato)
            ->where('fk_id_setor', $setor)
            ->first();
        $contagem = (new ContagemDeTempoService())
        ->contagemDeTempo($idCandidato, $setor);

        return [
            'dados' => $cadosCandidato,
            'setor'=> $dadosSetor,
            'contrato' => $dadosContrato,
            'diasTrabalhados' => $contagem['diasTrabalhados'],
            'diasFerias' => $contagem['diasFerias'],
            'feriasTiradas' => $contagem['feriasTiradas'],
            'titulo' => 'Comunicação de contrato expirando'
        ];
        
    }

    public function dadosComunicacao(): array
    {
        return [
            'email_candidato'   => $this->request->getPost('ds_email'),
            'nome_candidato'    => $this->request->getPost('ds_nome'),
            'email_setor'       => $this->request->getPost('ds_email_setor'),
            'nome_setor'        => $this->request->getPost('ds_nome_setor'),
            'assunto'           => $this->request->getPost('ds_assunto'),
            'mensagem_usuario'  => $this->request->getPost('ds_texto_observacao'),
            'data_encerramento' => $this->request->getPost('ds_data_encerramento'),
            'prorrogado'        => (int) $this->request->getPost('ds_prorrogado'),
            'fk_id_candidato'   => (int) $this->request->getPost('fk_id_candidato'),
            'fk_id_setor'       => (int) $this->request->getPost('fk_id_setor'),
        ];
    }
}