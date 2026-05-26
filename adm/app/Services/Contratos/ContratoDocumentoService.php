<?php
/*
Quem usa:
Control: Contratos.php
*/
namespace App\Services\Contratos;
use chillerlan\QRCode\{QRCode, QROptions};
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Exceptions\ContratoNaoEncontradoException;

use App\Models\DadosContratosModel;
use App\Models\AuxiliosModel;
use App\Models\InstituicoesModel;
use App\Models\SegurosModel;
use App\Models\DadosPrefeituraModel;
use App\Models\CandidatosModel;
use App\Models\AcademicosModel;
use App\Models\SetoresModel;
use App\Models\DadosAditivosModel;
use App\Models\DadosRescisaoModel;
use App\Models\MotivosRescisaoModel;


class ContratoDocumentoService
{
    public function __construct(
        private DadosContratosModel $contratosModel,
        private AuxiliosModel $auxiliosModel,
        private InstituicoesModel $instituicoesModel,
        private SegurosModel $segurosModel,
        private DadosPrefeituraModel $prefeituraModel,
        private CandidatosModel $candidatosModel,
        private AcademicosModel	$academicosModel,
        private SetoresModel   $setoresModel,
        private DadosAditivosModel $dadosAditivosModel,
        private DadosRescisaoModel $rescisaoModel,
        private MotivosRescisaoModel $motivosModel

    ) {}

    public function montarContrato(int $idContrato): array{
        $dadosContrato = $this->contratosModel->getContratosPorId($idContrato);

        if (!$dadosContrato) {
            throw new ContratoNaoEncontradoException('Contrato não encontrado');
        }
        list($ano, $mes, $dia) = explode('-', $dadosContrato->ds_nascimento);
        $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
        $dia = str_pad($dia, 2, '0', STR_PAD_LEFT);
        $dataNascimento = $dia . '/' . $mes . '/' . $ano;

        list($ano, $mes, $dia) = explode('-', $dadosContrato->ds_data_ingresso);
        $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
        $dia = str_pad($dia, 2, '0', STR_PAD_LEFT);
        $dataIngresso = $dia . '/' . $mes . '/' . $ano;
            
        list($ano, $mes, $dia) = explode('-', $dadosContrato->ds_data_encerramento);
        $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
        $dia = str_pad($dia, 2, '0', STR_PAD_LEFT);
        $dataEncerramento = $dia . '/' . $mes . '/' . $ano;

        $dadosAuxilio      = $this->auxiliosModel->find($dadosContrato->fk_id_auxilio);
        $dadosInstituicao  = $this->instituicoesModel->find($dadosContrato->fk_id_instituicao);
        $dadosSeguro       = $this->segurosModel->find($dadosContrato->fk_id_seguro);
        $dadosPrefeitura   = $this->prefeituraModel->first();

        $turno = match ($dadosContrato->ds_turno) {
            1 => 'Manhã',
            2 => 'Tarde',
            default => 'Manhã/Tarde'
        };

        return [
            'brasao'        => imageToBase64(ROOTPATH . '/external/img/logo.png'),
            'fundo'         => imageToBase64(ROOTPATH . '/external/img/fundo.jpg'),

            'estagiario'            => $dadosContrato->ds_nome,
            'cpf'                   => mask($dadosContrato->ds_cpf, '###.###.###-##'),
            'titulo_eleitor'        => '1',//mask($dadosContrato->ds_titulo_eleitor, '#### #### ####'),
            'nascimento'            => $dataNascimento,
            'rua'                   => $dadosContrato->ds_rua,
            'numero'                => $dadosContrato->ds_numero,
            'complemento'           => $dadosContrato->ds_complemento,
            'bairro'                => $dadosContrato->ds_nome_bairro,
            'cidade'                => $dadosContrato->ds_cidade,
            'cep'                   => mask($dadosContrato->ds_cep, '##.###-###'),
            'uf'                    => $dadosContrato->ds_uf,
            'telefone'              => mask($dadosContrato->ds_celular, '(##) # ####-####'),
            'email'                 => $dadosContrato->ds_email,
            'convenio'              => substr($dadosInstituicao->ds_numero_convenio, 0, -4) . '/' . substr($dadosInstituicao->ds_numero_convenio, -4),
            'faculdade'             => $dadosInstituicao->ds_nome,
            'representante_faculdade' => $dadosContrato->ds_representante_faculdade,
            'endereco_instituicao'  => $dadosInstituicao->ds_rua.', '.$dadosInstituicao->ds_numero.', '.
            $dadosInstituicao->ds_bairro.', '.$dadosInstituicao->ds_cidade.' - '.$dadosInstituicao->ds_estado.', '.
            mask($dadosInstituicao->ds_cep,'##.###-###'),
            'curso'                 => $dadosContrato->ds_nome_curso,
            'periodo'               => $dadosContrato->ds_periodo,
            'matricula'             => $dadosContrato->ds_registro_academico,
            'orientador'            => $dadosContrato->ds_orientador,
            'supervisor'            => $dadosContrato->ds_supervisor,
            'instituicao_cnpj'      => mask($dadosInstituicao->ds_cnpj, '##.###.###/####-##'),
            'instituicao_endereco'  => $dadosInstituicao->ds_rua.', '.$dadosInstituicao->ds_numero.', '.
                                       $dadosInstituicao->ds_bairro.', '.$dadosInstituicao->ds_cidade.' - '.
                                       $dadosInstituicao->ds_estado.', '.mask($dadosInstituicao->ds_cep,'##.###-###'),
            'instituicao_telefone'  => $dadosInstituicao->ds_telefone,

            'concedente_nome'           => $dadosPrefeitura->ds_nome_prefeitura,
            'concedente_cnpj'           => mask($dadosPrefeitura->ds_cnpj, '##.###.###/####-##'), 
            'concedente_endereco'       => $dadosPrefeitura->ds_rua.', '.$dadosPrefeitura->ds_numero.', '.
                                            $dadosPrefeitura->ds_bairro.', '.$dadosPrefeitura->ds_municipio.' - '.$dadosPrefeitura->ds_estado,
            'concedente_telefone'       => mask($dadosPrefeitura->ds_telefone, '(##) ####-####'),
            'nome_secretaria'           => $dadosContrato->ds_nome_secretaria,
            'setor_estagio'             => $dadosContrato->ds_nome_setor,
            'supervisor_nome'           => $dadosContrato->ds_supervisor,
            'supervisor_cargo'          => $dadosContrato->ds_cargo_supervisor,
            'diretor_recursos_humanos'  => $dadosPrefeitura->ds_diretor_rh,
            
            'edital'                    => $dadosContrato->ds_numero_edital,
            'numero_contrato'           => $dadosContrato->ds_numero_termo.'/'.$dadosContrato->ds_ano_termo,
            'dataInicio'                => $dataIngresso,
            'dataFim'                   => $dataEncerramento,
            'data_assinatura'           => date('d/m/Y'),
            'carga_horaria_diaria'      => $dadosAuxilio->ds_horas_diarias,
            'carga_extenso'             => numero_por_extenso($dadosAuxilio->ds_horas_diarias),
            'carga_horaria_semanal'     => $dadosAuxilio->ds_horas_diarias * 5 .' horas',
            'dias_semana'               => 'Segunda a Sexta-feira',
            'turno'                     => $turno,
            'recesso'                   => '30 dias a cada 12 meses, ou proporcional',
            'valor_bolsa'               => 'R$'. number_format($dadosAuxilio->ds_valor_bolsa, 2, ',', '.'), 
            'bolsa_extenso'             => valor_por_extenso($dadosAuxilio->ds_valor_bolsa),
            'forma_pagamento'           => 'Depósito em conta até o 5º dia útil de cada mês',

            // === Seguro Obrigatório ===
            'seguro_empresa'            => ($dadosSeguro->ds_seguradora == "") ?  'não informado': $dadosSeguro->ds_seguradora,
            'seguro_numero'             => ($dadosSeguro->ds_numero_seguro == 0) ?  'não informado': $dadosSeguro->ds_numero_seguro,
            'seguro_valor'              => ($dadosSeguro->ds_apolice == 0)  ?  'não informado': 'R$'. number_format($dadosSeguro->ds_apolice, 2, ',', '.'),

            // === Cláusulas e Observações ===
            'plano_atividades'          => 'anexa ao termo',
            'observacoes'               => 'Preencher informações adicionais, se necessário',

            // === Assinaturas ===
            'assinatura_estagiario'     => '__________________________',
            'assinatura_concedente'     => '__________________________',
            'assinatura_instituicao'    => '__________________________',
            'testemunha_1'              => 'Preencher nome e CPF da testemunha 1',
            'testemunha_2'              => 'Preencher nome e CPF da testemunha 2',

            // === Verificação ===
            'qrCode'     => '<img src="'.$this->gerarQRCode($dadosContrato->ds_verificador).'" />',
            'verificador'  => $dadosContrato->ds_verificador,

            // === Layout e Identificação do Documento ===
            'titulo_documento' => 'TERMO DE COMPROMISSO DE ESTÁGIO Nº '.$dadosContrato->ds_numero_termo.'/'.$dadosContrato->ds_ano_termo,
            'subtitulo_documento' => 'Conforme Lei Federal nº 11.788, de 25 de setembro de 2008',
            'local_assinatura' => 'Divinópolis/MG',
        ];
    }

    public function imprimirAditivo(int $idContrato): array{
         $dadosContrato = $this->contratosModel->getContratosPorId($idContrato);

        if (!$dadosContrato) {
            throw new PageNotFoundException('Contrato não encontrado');
        }

        $alterarCargaHoraria = false;
        $alterarSupervisor = false;
        $alterarLotacao = false;
        $cargaHorariaAntiga = '';
        $extensoCargaHorariaAntiga = '';
        $cargaHorariaNova = '';
        $extensoCargaHorariaNova = '';
        $remuneracaoAntiga = '';
        $extensoRemuneracaoAntiga = '';
        $remuneracaoNova = '';
        $extensoRemuneracaoNova = '';
        $supervisorNovo = '';
        $lotacaoNova = '';

        
        $dadosCandidato         =   $this->candidatosModel->where('pk_id_candidato', $dadosContrato->pk_id_candidato)->first();
        $dadosAcademico         =   $this->academicosModel->where('pk_id_candidato', $dadosContrato->pk_id_candidato)->first();
        
        $dadosAditivo           =   $this->dadosAditivosModel->getAditivoPorContrato($idContrato);

        if($dadosContrato->fk_id_auxilio != $dadosAditivo->ds_carga_horaria && $dadosAditivo->ds_carga_horaria != 0){
            $alterarCargaHoraria = true;
            $dadosAuxilioAntigo  =   $this->auxiliosModel->where('pk_id_auxilio', $dadosContrato->fk_id_auxilio)->first();
            $dadosAuxilioNovo    =   $this->auxiliosModel->where('pk_id_auxilio', $dadosAditivo->ds_carga_horaria)->first();

            $cargaHorariaAntiga  =   $dadosAuxilioAntigo->ds_horas_diarias;
            $extensoCargaHorariaAntiga = numero_por_extenso($cargaHorariaAntiga);

            $cargaHorariaNova    =   $dadosAuxilioNovo->ds_horas_diarias;
            $extensoCargaHorariaNova = numero_por_extenso($cargaHorariaNova);

            $remuneracaoAntiga   =   $dadosAuxilioAntigo->ds_valor_bolsa;
            $extensoRemuneracaoAntiga = valor_por_extenso($remuneracaoAntiga);

            $remuneracaoNova     =   $dadosAuxilioNovo->ds_valor_bolsa;
            $extensoRemuneracaoNova = valor_por_extenso($remuneracaoNova);
            
        }

        if(!empty($dadosAditivo->ds_supervisor)){
            $alterarSupervisor = true;
            $supervisorNovo = $dadosAditivo->ds_supervisor;
        }

        if($dadosContrato->pk_id_setor != $dadosAditivo->fk_id_setor && $dadosAditivo->fk_id_setor != 0){
            $alterarLotacao = true;
            $dadosSetor = $this->setoresModel->where('pk_id_setor', $dadosAditivo->fk_id_setor)->first();
            $lotacaoNova = $dadosSetor->ds_nome_setor;
        }

        list($ano, $mes, $dia) = explode('-', $dadosAditivo->ds_data_aditivo);
            $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
            $dia = str_pad($dia, 2, '0', STR_PAD_LEFT);
            $dataAditivo = $dia . '/' . $mes . '/' . $ano;

        list($pano, $pmes, $pdia) = explode('-', $dadosAditivo->ds_data_prorrogacao);
            $pmes = str_pad($pmes, 2, '0', STR_PAD_LEFT);
            $pdia = str_pad($pdia, 2, '0', STR_PAD_LEFT);
            $dataProrrogacao = $pdia . '/' . $pmes . '/' . $pano;

        $mesData = (int) date('m');

        

        return  [
            // === Identificação visual ===
            'brasao'                    => imageToBase64(ROOTPATH . '/external/img/logo.png'),
            'fundo'                     => imageToBase64(ROOTPATH . '/external/img/fundo.jpg'),
            'titulo_documento'          => 'Termo Aditivo nº '.$dadosAditivo->ds_numero_aditivo."/".$dadosAditivo->ds_ano_aditivo,
            'nome_estagiario'           => $dadosCandidato->ds_nome,
            'cpf_estagiario'            => $dadosCandidato->ds_cpf,
            'data_aditivo'              => $dataAditivo,
            'data_prorrogacao'          => $dataProrrogacao,
            'numero_termo'              => $dadosContrato->ds_numero_termo."/".$dadosContrato->ds_ano_termo,
            'numero_aditivo'            => $dadosAditivo->ds_numero_aditivo."/".$dadosAditivo->ds_ano_aditivo,
            'alterar_carga_horaria'     => $alterarCargaHoraria,
            'carga_horaria_antiga'      => $cargaHorariaAntiga,
            'extenso_carga_horaria_antiga' => $extensoCargaHorariaAntiga,
            'carga_horaria_nova'        => $cargaHorariaNova,
            'extenso_carga_horaria_nova' => $extensoCargaHorariaNova,
            'remuneracao_antiga'        => 'R$'. number_format($remuneracaoAntiga, 2, ',', '.'), 
            'extenso_remuneracao_antiga' => $extensoRemuneracaoAntiga,
            'remuneracao_nova'          => 'R$'. number_format($remuneracaoNova, 2, ',', '.'), 
            'extenso_remuneracao_nova'  => $extensoRemuneracaoNova,
            'alterar_supervisor'        => $alterarSupervisor,
            'supervisor_novo'           => $supervisorNovo,
            'alterar_lotacao'           => $alterarLotacao,
            'lotacao_nova'              => $lotacaoNova,
            'local_assinatura'          => 'Divinópolis',
            'data_assinatura'           => date('d'). ' de '. mesPorExtenso($mesData). ' de '.date('Y'),
            'assinatura_estagiario'     => '                          ',
            'assinatura_secretario'     => '                          ',
            'nome_secretario'           => 'MARIANA BORGES CAMPOS DOS SANTOS',

        ];
    }         

    public function imprimirRescisao(int $idContrato): array{
        $dadosContrato = $this->contratosModel->getContratosPorId($idContrato);

        $dadosCandidato         =   $this->candidatosModel->where('pk_id_candidato', $dadosContrato->pk_id_candidato)->first();
        $dadosAcademico         =   $this->academicosModel->where('pk_id_candidato', $dadosContrato->pk_id_candidato)->first();
        $dadosRescisao          =   $this->rescisaoModel->getRescisaoPorContrato($idContrato);

        $motivo                 =   $this->motivosModel->where('pk_id_motivo', $dadosRescisao->fk_id_motivo)->first();

        list($ano, $mes, $dia) = explode('-', $dadosRescisao->ds_data_baixa);
            $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
            $dia = str_pad($dia, 2, '0', STR_PAD_LEFT);
            $dataBaixa = $dia . '/' . $mes . '/' . $ano;
            
        $mesData = (int) date('m');

        

        return [
            // === Identificação visual ===
            'brasao'                    => imageToBase64(ROOTPATH . '/external/img/logo.png'),
            'fundo'                     => imageToBase64(ROOTPATH . '/external/img/fundo.jpg'),
            'titulo_documento'          => 'TERMO DE RESCISÃO DE ESTÁGIO',
            'nome_estagiario'           => $dadosCandidato->ds_nome,
            'cpf_estagiario'            => $dadosCandidato->ds_cpf,
            'data_baixa'                => $dataBaixa,
            'numero_termo'              => $dadosContrato->ds_numero_termo."/".$dadosContrato->ds_ano_termo,
            'motivo'                    => $motivo->ds_descricao_motivo,
            'local_assinatura'          => 'Divinópolis',
            'data_assinatura'           => date('d'). ' de '. mesPorExtenso($mesData). ' de '.date('Y'),
            'assinatura_estagiario'     => '                          ',
            'assinatura_secretario'     => '                          ',
            'nome_secretario'           => 'MARIANA BORGES CAMPOS DOS SANTOS',

        ];
    }
    
    private function gerarQRCode(string $verificador): string{
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'scale'      => 3,
        ]);

        $qrcode = new QRCode($options);

        return $qrcode->render(
            site_url('Cadastros/verificar/' . $verificador)
        );
    }

}

