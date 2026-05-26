<?php

namespace App\Controllers;
use App\Helpers\EmailHelper;
use CodeIgniter\Controller;

use App\Models\CandidatosModel;
use App\Models\AcademicosModel;
use App\Models\MigradorModel;
use App\Models\AdequarModel;
use App\Models\EditaisModel;
use App\Models\ConvocadosModel;
use App\Models\DadosContratosModel;
use App\Models\DadosAditivosModel;

use App\Models\CursosModel;
use App\Models\EditaisCursosModel;
use App\Models\EditaisCandidatosModel;


use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Migrador extends BaseController{
    public function migrar(){
        
        $migradorModel = new MigradorModel();

        $dadosAntigos = $migradorModel->getMigracoes();
        
        foreach($dadosAntigos as $dados){
            $dadosPessoais = array(
                'ds_nome'           => $dados->ds_nome,
                'ds_cpf'            => $dados->ds_cpf,
                'ds_nascimento'     => $dados->ds_nascimento,
                'ds_nome_mae'       => $dados->ds_mae,
                'fk_id_pne'         => $dados->ds_pne,
                'ds_outrapne'       => $dados->ds_outrapne,
                'ds_cep'            => $dados->ds_cep,
                'ds_rua'            => $dados->ds_rua,
                'ds_numero'         => $dados->ds_numero,
                'ds_complemento'    => $dados->ds_complemento,
                'ds_nome_bairro'    => $dados->ds_bairro,
                'ds_cidade'         => $dados->ds_cidade,
                'ds_uf'             => $dados->ds_estado,
                'ds_celular'        => preg_replace('/[^0-9]/', '', $dados->ds_telefonecelular),
                'ds_fixo'           => preg_replace('/[^0-9]/', '', $dados->ds_telefoneresidencial),
                'ds_email'          => $dados->ds_email,
                'ds_data_cadastro'  => $dados->ds_datacadastro,
                'ds_hora_cadastro'  => $dados->ds_horacadastro,
            );
           
            $candidatosModel = new CandidatosModel();
            $isDuplicado = $candidatosModel->where('ds_cpf', $dados->ds_cpf)->first();
            if($isDuplicado){
                continue; // Pula para a próxima iteração do loop
            }
             
            if(!$candidatosModel->insert($dadosPessoais)){
                echo "Erro ao inserir candidato: " . implode(", ", $candidatosModel->errors());
                die();
            }
            $idCandidato = $candidatosModel->getInsertID();
            $dadosAcademicos = array(
                'pk_id_candidato' => $idCandidato,
                'fk_id_curso' => $dados->fk_id_curso,
                'ds_ensino_medio' => $dados->ds_ensinomedio,
                'ds_categoria' => $dados->ds_catensino,
                'ds_periodo' => $dados->ds_faseperiodo,
                'ds_faculdade' => $dados->ds_instituicao,
                'ds_manha' => ($dados->ds_manha == 'M')? '1' : '0',
                'ds_tarde' => ($dados->ds_tarde == 'T')? '1' : '0',
                'ds_noite' => ($dados->ds_noite == 'N')? '1' : '0',
                'ds_data_cadastro' => $dados->ds_datacadastro,
                'ds_hora_cadastro' => $dados->ds_horacadastro,
            );
            $academicosModel = new AcademicosModel();

            if(!$academicosModel->insert($dadosAcademicos)){
                echo "Erro ao inserir acadêmico: " . implode(", ", $academicosModel->errors());
                die();      
            }
            
            $editaisCursosModel = new EditaisCursosModel();
            $editaisCandidatosModel = new EditaisCandidatosModel();

            $codigoCurso = $editaisCursosModel->where('fk_id_curso', $dados->fk_id_curso)->first();
            if(!$codigoCurso){
                continue;
            }
            $codigoEdital = $dados->fk_id_edital;
            $dadosEditaisCandidatos = array(
                'fk_id_edital'      => $codigoEdital,
                'fk_id_candidato'   => $idCandidato,
                'ds_observacao'     => "Vinculo gerado por migração de dados.",
                'ds_status'         => '1',
            );

            if(!$editaisCandidatosModel->insert($dadosEditaisCandidatos)){
                echo "Erro ao inserir edital-candidato: " . implode(", ", $editaisCandidatosModel->errors());
                die();
            };
        }
    }

    public function adequarContratos()
{
    $db = db_connect();

    $candidatosModel          = new CandidatosModel();
    $academicosModel          = new AcademicosModel();
    $migradorModel            = new MigradorModel();
    $adequarModel             = new AdequarModel();
    $convocadosModel          = new ConvocadosModel();
    $editaisCandidatosModel   = new EditaisCandidatosModel();
    $dadosContratosModel      = new DadosContratosModel();
    $dadosAditivosModel       = new DadosAditivosModel();

    $contratados = $adequarModel->where('fk_id_candidato >', '0')->orderBy('fk_id_edital')->findAll();
    

    foreach ($contratados as $contratado) {


        $db->transStart();

        try {

            // ======================================================
            // 1. RESOLVER CANDIDATO
            // ======================================================

            

            // Caso já exista vínculo
            
                $idCandidato = $contratado->fk_id_candidato;
             
            // Caso não exista, buscar no migrador e cadastrar
            
                
               /* $dadosMigrados = $migradorModel->getMigracoes($contratado->ds_cpf);
                
                
                if (!$dadosMigrados) {
                    throw new \Exception("CPF não encontrado no migrador: {$contratado->ds_cpf}");
                }
                $dadosMigrados = (object) $dadosMigrados[0];
                */


                // Verifica duplicidade por CPF
                //$duplicado = $candidatosModel->where('ds_cpf', $dadosMigrados->ds_cpf)->first();
                /*if ($duplicado) {
                    $idCandidato = $duplicado->pk_id_candidato;
                } else {

                    // ---------------------------
                    // CADASTRO PESSOAL
                    // ---------------------------
                    /*
                    $dadosPessoais = [
                        'ds_nome'             => $dadosMigrados->ds_nome,
                        'ds_cpf'              => $dadosMigrados->ds_cpf,
                        'ds_nascimento'       => $dadosMigrados->ds_nascimento,
                        'ds_nome_mae'         => $dadosMigrados->ds_mae,
                        'fk_id_pne'           => $dadosMigrados->ds_pne,
                        'ds_outrapne'         => $dadosMigrados->ds_outrapne,
                        'ds_cep'              => $dadosMigrados->ds_cep,
                        'ds_rua'              => $dadosMigrados->ds_rua,
                        'ds_numero'           => $dadosMigrados->ds_numero,
                        'ds_complemento'      => $dadosMigrados->ds_complemento,
                        'ds_nome_bairro'       => $dadosMigrados->ds_bairro,
                        'ds_cidade'           => $dadosMigrados->ds_cidade,
                        'ds_uf'               => $dadosMigrados->ds_estado,
                        'ds_celular'          => preg_replace('/[^0-9]/', '', $dadosMigrados->ds_telefonecelular),
                        'ds_fixo'             => preg_replace('/[^0-9]/', '', $dadosMigrados->ds_telefoneresidencial),
                        'ds_email'            => $dadosMigrados->ds_email,
                        'ds_data_cadastro'    => $dadosMigrados->ds_datacadastro,
                        'ds_hora_cadastro'    => $dadosMigrados->ds_horacadastro,
                    ];
                    

                    if (!$candidatosModel->insert($dadosPessoais)) {
                        throw new \Exception("Erro ao inserir candidato: " . implode(', ', $candidatosModel->errors()));
                    }

                    $idCandidato = $candidatosModel->getInsertID();
*/
                    // ---------------------------
                    // CADASTRO ACADÊMICO
                    // ---------------------------
                    /*
                    $dadosAcademicos = [
                        'pk_id_candidato' => $idCandidato,
                        'fk_id_curso'     => $contratado->fk_id_curso,
                        'ds_ensino_medio' => $dadosMigrados->ds_ensinomedio,
                        'ds_categoria'    => $dadosMigrados->ds_catensino,
                        'ds_periodo'      => $dadosMigrados->ds_faseperiodo,
                        'ds_faculdade'    => $dadosMigrados->ds_instituicao,
                        'ds_manha'        => ($dadosMigrados->ds_manha == 'M') ? '1' : '0',
                        'ds_tarde'        => ($dadosMigrados->ds_tarde == 'T') ? '1' : '0',
                        'ds_noite'        => ($dadosMigrados->ds_noite == 'N') ? '1' : '0',
                        'ds_data_cadastro'=> $dadosMigrados->ds_datacadastro,
                        'ds_hora_cadastro'=> $dadosMigrados->ds_horacadastro,
                    ];

                    if (!$academicosModel->insert($dadosAcademicos)) {
                        throw new \Exception("Erro ao inserir acadêmico: " . implode(', ', $academicosModel->errors()));
                    }
                }*/
            

            // ======================================================
            // 2. CRIAR CONVOCAÇÃO
            // ======================================================

            $dadosConvocados = [
                'fk_id_candidato'    => $idCandidato,
                'fk_id_edital'       => $contratado->fk_id_edital,
                'fk_id_curso'        => $contratado->fk_id_curso,
                'ds_comparecimento'  => '1',
                'ds_mensagem'        => '',
                'ds_data'            => date('Y-m-d'),
                'ds_hora'            => date('H:i:s'),
                'ds_interesse'       => '1',
                'ds_status'          => '2'
            ];

            if (!$convocadosModel->insert($dadosConvocados)) {
                //throw new \Exception("Erro ao inserir convocado: " . implode(', ', $convocadosModel->errors()));
                dd($convocadosModel->errors());
            }

            // ======================================================
            // 3. CRIAR CONTRATO
            // ======================================================

            $dadosContrato = [
                'ds_numero_termo'            => $contratado->ds_numero_termo,
                'ds_ano_termo'               => $contratado->ds_ano_termo,
                'ds_matricula'               => '',
                'fk_id_edital'               => $contratado->fk_id_edital,
                'fk_id_candidato'            => $contratado->fk_id_candidato,
                'fk_id_setor'                => $contratado->fk_id_setor,
                'fk_id_curso'                => $contratado->fk_id_curso,
                'ds_data_ingresso'           => $contratado->ds_data_ingresso,
                'ds_data_encerramento'       => $contratado->ds_data_encerramento,
                'ds_data_baixa'              => '',
                'ds_notificado'              => '',
                'ds_prorrogado'              => $contratado->ds_prorrogado ?? $contratado->ds_prorrodado,
                'fk_id_instituicao'          => 'conferir no TCE',
                'ds_representante_faculdade' => 'conferir no TCE',
                'ds_supervisor'              => 'conferir no TCE',
                'ds_cargo_supervisor'        => 'conferir no TCE',
                'ds_orientador'              => 'conferir no TCE',
                'fk_id_auxilio'              => '1',
                'ds_turno'                   => '3',
                'fk_id_seguro'               => '3',
                'ds_status'                  => '1'
            ];

            if (!$dadosContratosModel->insert($dadosContrato)) {
               // throw new \Exception("Erro ao inserir contrato: " . implode(', ', $dadosContratosModel->errors()));
                dd($dadosContratosModel->errors());
            }

            $idContrato = $dadosContratosModel->getInsertID();

            // ======================================================
            // 4. CRIAR ADITIVO (SE PRORROGADO)
            // ======================================================

            $prorrogado = $contratado->ds_prorrogado ?? $contratado->ds_prorrodado;

            if ($prorrogado == '1') {

                $dadosAditivo = [
                    'fk_id_contrato'        => $idContrato,
                    'ds_numero_aditivo'     => $contratado->ds_numero_aditivo,
                    'ds_ano_aditivo'        => $contratado->ds_ano_aditivo,
                    'fk_id_setor'           => $contratado->fk_id_setor,
                    'ds_supervisor'         => 'conferir no TCE',
                    'ds_carga_horaria'      => '3',
                    'ds_data_aditivo'       => date('Y-m-d', strtotime('2025-12-31')),
                    'ds_data_prorrogacao'   => $contratado->ds_data_encerramento,
                    'ds_status'             => '1'
                ];

                if (!$dadosAditivosModel->insert($dadosAditivo)) {
                    //throw new \Exception("Erro ao inserir aditivo: " . implode(', ', $dadosAditivosModel->errors()));
                    dd($dadosAditivosModel->errors());
                }
            }

            // ======================================================
            // 5. VINCULAR CANDIDATO AO EDITAL
            // ======================================================
/*
            $dadosVinculo = [
                'fk_id_edital'     => $contratado->fk_id_edital,
                'fk_id_candidato'  => $contratado->fk_id_candidato,
                'fk_id_curso'      => $contratado->fk_id_curso,
                'ds_observacao'    => 'Inserido através de rotina de migração',
                'ds_status'        => '1'
            ];

            if(!$editaisCandidatosModel->insert($dadosVinculo)){
                //throw new \Exception("Erro ao vincular candidato ao edital: " . implode(', ', $editaisCandidatosModel->errors()));
                dd($editaisCandidatosModel->errors());
            }
*/
            $db->transComplete();

        } catch (\Throwable $e) {
            /*
            $db->transRollback();
            log_message('error', $e->getMessage());
            continue;
            */
             dd($e->getMessage(), $contratado);
        }
    }

    return "Migração finalizada com sucesso.";
    }



}