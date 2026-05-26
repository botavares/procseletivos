<?php
/*Quem usa:
Control: Contratos.php
*/
namespace App\Services\Contratos;

use CodeIgniter\HTTP\IncomingRequest;

use App\Models\AcademicosModel;
use App\Models\CandidatosModel;
use App\Models\CursosModel;
use App\Models\DadosContratosModel;
use App\Models\SegurosModel;
use App\Models\EditaisCandidatosModel;
use App\Models\VagasModel;
use App\Models\AuxiliosModel;
use App\Models\InstituicoesModel;
use App\Models\SetoresModel;
use App\Models\MotivosRescisaoModel;


class ContratoFormService{
    protected IncomingRequest $request;
     public function __construct(IncomingRequest $request) {
        $this->request = $request;
    }
    public function prepararFormularioNovo(int $idCandidato): array
    {
        $academicosModel        = new AcademicosModel();
        $candidatosModel        = new CandidatosModel();
        $cursosModel            = new CursosModel();
        $editaisCandidatosModel = new EditaisCandidatosModel();
        $vagasModel             = new VagasModel();
        $auxiliosModel          = new AuxiliosModel();
        $instituicoesModel      = new InstituicoesModel();

        $dadosAcademicos = $academicosModel
            ->where('pk_id_candidato', $idCandidato)
            ->first();

        if (!$dadosAcademicos) {
            throw new \RuntimeException('Dados acadêmicos não encontrados');
        }

        $dadosCandidato = $candidatosModel->find($idCandidato);
        $dadosCurso     = $cursosModel->find($dadosAcademicos->fk_id_curso);
        $dadosEdital    = $editaisCandidatosModel->editaisPorIdCandidato($idCandidato);

        return [
            'academicos'     => $dadosAcademicos,
            'candidato'      => $dadosCandidato,
            'curso'          => $dadosCurso->pk_id_curso,
            'cursoNome'      => $dadosCurso->ds_nome_curso,
            'auxilios'       => $auxiliosModel->findAll(),
            'instituicoes'   => $instituicoesModel->orderBy('ds_nome', 'ASC')->findAll(),
            'edital'         => $dadosEdital->fk_id_edital ?? null,
            'setoresComVagas'=> $setoresComVagas = $vagasModel->setoresComVagas($dadosAcademicos->fk_id_curso),
            'titulo'         => 'Contratar Candidato ',
        ];
    }

    public function dadosContrato(): array{
        $contratosModel = new DadosContratosModel();
        $acharTermo = $contratosModel->selectMax('ds_numero_termo')->where('ds_ano_termo', date('Y'))->first();
        $numeroTermo = $acharTermo->ds_numero_termo + 1;
        $segurosModel = new SegurosModel();
        $seguroAtivo = $segurosModel->where('ds_status', 1)->first();

        
         return [
            'fk_id_candidato'               => $this->request->getPost('fk_id_candidato'),
            'ds_numero_termo'               => $numeroTermo,
            'ds_ano_termo'                  => date('Y'),
            'fk_id_setor'                   => $this->request->getPost('fk_id_setor'),
            'ds_data_ingresso'              => $this->dataBanco('ds_data_ingresso'),
            'ds_data_encerramento'          => $this->dataBanco('ds_data_encerramento'),
            'ds_status'                     => 1,
            'ds_data_baixa'                 => null,
            'fk_id_edital'                  => $this->request->getPost('fk_id_edital'),
            'fk_id_curso'                   => $this->request->getPost('fk_id_curso'),
            'ds_notificado'                 => 0,
            'ds_prorrogado'                 => 0,
            'ds_supervisor'                 => $this->request->getPost('ds_supervisor'),
            'ds_cargo_supervisor'           => $this->request->getPost('ds_cargo_supervisor'),
            'fk_id_instituicao'             => $this->request->getPost('fk_id_instituicao'),
            'ds_orientador'                 => $this->request->getPost('ds_orientador'),
            'ds_representante_faculdade'    => $this->request->getPost('ds_representante_faculdade'),
            'fk_id_auxilio'                 => $this->request->getPost('fk_id_auxilio'),
            'ds_matricula'                  => $this->request->getPost('ds_matricula'),
            'ds_turno'                      => $this->request->getPost('ds_turno'),
            'fk_id_seguro'                  => $seguroAtivo->pk_id_seguro,
            'ds_supervisor'                 => $this->request->getPost('ds_supervisor'),
            'ds_cargo_supervisor'           => $this->request->getPost('ds_cargo_supervisor'),
        ];
    }

    private function dataBanco(string $campo): ?string {
        $valor = $this->request->getPost($campo);

        if (!$valor) {
            return null;
        }

        [$d, $m, $y] = explode('/', $valor);
        return "{$y}-{$m}-{$d}";
    }
    /*====================================================================
    *REFERENTE A ALTERAÇÕES NO CONTRATO
    ======================================================================*/
    public function prepararFormularioAlteracao(int $idContrato): array{
        $contratosModel         = new DadosContratosModel();
        $candidatosModel        = new CandidatosModel();
        $cursosModel            = new CursosModel();
        $auxiliosModel          = new AuxiliosModel();
        $instituicoesModel      = new InstituicoesModel();
        $vagasModel             = new VagasModel();
        $editaisCandidatosModel = new EditaisCandidatosModel();
        $setoresModel           = new SetoresModel();
        

        $contrato = $contratosModel->find($idContrato);

        if (!$contrato) {
            throw new \RuntimeException('Contrato não encontrado');
        }

        $candidato          = $candidatosModel->find($contrato->fk_id_candidato);
        $curso              = $cursosModel->find($contrato->fk_id_curso);
        $dadosEdital        = $editaisCandidatosModel->editaisPorIdCandidato($contrato->fk_id_candidato);
        
        $cursosEditais      = $cursosModel->getCursosByEdital($dadosEdital->fk_id_edital);
        $dadosSetor         = $setoresModel->find( $contrato->fk_id_setor);
        $dadosInstituicao   = $instituicoesModel->find($contrato->fk_id_instituicao);

        return [
            'modo'              => 'alterar',
            'contrato'          => $contrato,
            'candidato'         => $candidato,
            'curso'             => $curso->pk_id_curso,
            'cursoNome'         => $curso->ds_nome_curso,
            'cursosEdital'      => $cursosEditais,
            'auxilio'           => $contrato->fk_id_auxilio,
            'auxilioNome'       => $auxiliosModel->find($contrato->fk_id_auxilio)->ds_horas_diarias,
            'auxilios'          => $auxiliosModel->findAll(),
            'edital'            => $dadosEdital->fk_id_edital ?? null,
            'instituicao'       => $dadosInstituicao->pk_id_instituicao ?? null,
            'instituicaoNome'   => $dadosInstituicao->ds_nome ?? null,
            'instituicoes'      => $instituicoesModel->orderBy('ds_nome', 'ASC')->findAll(),
            'setor'             => $dadosSetor->pk_id_setor,
            'setorNome'         => $dadosSetor->ds_nome_setor,
            'setoresComVagas'   => $vagasModel->setoresComVagas($contrato->fk_id_curso),
            'titulo'            => 'Alterar Contrato',
            'action'            => 'update'
        ];
    }
    public function dadosAlterarContrato(): array{
        return [
         
        'pk_id_contrato'             => $this->request->getPost('pk_id_contrato'),
        'fk_id_cursoAntigo'          => $this->request->getPost('fk_id_cursoAntigo'),
        'fk_id_setorAntigo'          => $this->request->getPost('fk_id_setorAntigo'),
        'fk_id_curso'                => $this->request->getPost('fk_id_curso'),
        'fk_id_setor'                => $this->request->getPost('fk_id_setor'),
        'ds_data_ingresso'           => $this->dataBanco('ds_data_ingresso'),
        'ds_data_encerramento'       => $this->dataBanco('ds_data_encerramento'),
        'ds_supervisor'              => $this->request->getPost('ds_supervisor'),
        'ds_cargo_supervisor'        => $this->request->getPost('ds_cargo_supervisor'),
        'fk_id_instituicao'          => $this->request->getPost('fk_id_instituicao'),
        'ds_orientador'              => $this->request->getPost('ds_orientador'),
        'ds_representante_faculdade' => $this->request->getPost('ds_representante_faculdade'),
        'fk_id_auxilio'              => $this->request->getPost('fk_id_auxilio'),
        'ds_turno'                   => $this->request->getPost('ds_turno'),
        
        
   
        ];
    }

    public function formAditivoRescindir(int $idContrato): array{
        $modelCursos            = new CursosModel();
        $modelSetor             = new SetoresModel();
        $modelAcademicos        = new AcademicosModel();
        $modelContratos         = new DadosContratosModel();
        $modelCandidato         = new CandidatosModel();
        $modelMotivosRescisao   = new MotivosRescisaoModel();
        $modelAuxilios          = new AuxiliosModel();
        $modelVagas             = new VagasModel();

        $motivos                = $modelMotivosRescisao->orderBy('pk_id_motivo')->findAll();

        $dadosContrato          = $modelContratos->find($idContrato);
        if (!$dadosContrato) {
            throw new \RuntimeException('Contrato não encontrado');
        }

        $dadosAcademicos        = $modelAcademicos->find($dadosContrato->fk_id_candidato);

        if (!$dadosAcademicos) {
            throw new \RuntimeException('Dados acadêmicos não encontrados');
        }

        $dadosCurso             = $modelCursos->find($dadosAcademicos->fk_id_curso);
        $dadosSetor             = $modelSetor->find($dadosContrato->fk_id_setor);
        $dadosContratado        = $modelCandidato->find($dadosContrato->fk_id_candidato);
        $setoresComVagas        = $modelVagas->setoresComVagas($dadosAcademicos->fk_id_curso);
        $dadosAuxilio           = $modelAuxilios->orderBy('ds_horas_diarias', 'ASC')->findAll();
        //$prorrogado             = $dadosContrato->ds_prorrogado;
        return [
            'dadosContrato'     => $dadosContrato,
            'dadosAuxilio'      => $dadosAuxilio,
            'contratado'        => $dadosContratado,
            'vagas'             => $setoresComVagas,
            'nomeCurso'         => $dadosCurso->ds_nome_curso,
            'idSetor'           => $dadosSetor->pk_id_setor,
            'nomeSetor'         => $dadosSetor->ds_nome_setor,
            'motivos'           => $motivos,
            'setores'           => $modelSetor->orderBy('ds_nome_setor')->findAll(),
            //'prorrogado'        => $prorrogado, // se ele estiver prorrogado o formulário impede nova prorrogação
            'titulo'            => "Prorrogar ou Encerrar contrato",
        ];
    }
    public function dadosAditivoRescindir(): array{
        return [
            'modo'                  =>  $this->request->getPost('ds_modo'),
            'idContrato'            =>  $this->request->getPost('pk_id_contrato'),
            'idCandidato'           =>  $this->request->getPost('fk_id_candidato'),
            'termo'                 =>  $this->request->getPost('ds_numero_termo'),
            'anoTermo'              =>  $this->request->getPost('ds_ano_termo'),
            'fk_id_setor'           =>  $this->request->getPost('fk_id_setor'),
            'ds_supervisor'         =>  $this->request->getPost('ds_supervisor'),
            'ds_data_rescisao'      =>  $this->dataBanco('ds_data_rescisao'),
            'ds_carga_horaria'      =>  $this->request->getPost('ds_carga_horaria'),
            'ds_motivo_rescisao'    =>  $this->request->getPost('ds_motivo_rescisao'),
            'ds_data_prorrogacao'   =>  $this->dataBanco('ds_data_prorrogacao'),
            'dataBaixa'             =>  $this->dataBanco('ds_data_baixa'),
            'motivo'                =>  $this->request->getPost('pk_id_motivo'),

        ];
    }
    
}

