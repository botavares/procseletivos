<?php 
namespace App\Traits;
use App\Models\ManifestosAndamentoModel;
use App\Models\ManifestacoesModel;
trait AtualizarAndamentoTrait
{

    /*==============================================================================
        FUNÇÃO: atualizarAndamento;
        OBJETIVO: Atualizar o andamento da manifestação;
        PARAMETROS: $idManifesto, $dadosRecebidos;
        CRIAÇÃO: 05/12/2024
        MODIFICADO:
        RESUMO: Toda vez que for gerada alguma resposta, contestação e finalização do 
                andamento, essa função deve ser chamada para atualizar o andamento da 
                manifestação. 
    ==============================================================================*/
    public function atualizarAndamento($idResposta, $dadosRecebidos, $tipoAndamento)
    {
        // Carregar modelo de andamentos
        $andamentoManifesto = new ManifestosAndamentoModel();
        $andamento = $andamentoManifesto->where('fk_id_manifesto', $dadosRecebidos['fk_id_manifesto'])
            ->orderBy('pk_id_andamento', 'DESC')
            ->first();

        if (!$andamento) {
            return redirect()->route('Manifestacoes')->with('error', 'Manifestação não encontrada.');
        }

        $idManifesto = $andamento->fk_id_manifesto;
        $tramitado = $andamento->ds_tramitado;
        $respondido = $andamento->ds_respondido;
        $encerrado = $andamento->ds_encerrado;
        $observacao = "";
        $botaoAcessar = "";

        // Definir status com base no tipo de andamento
        switch ($tipoAndamento) {
            case 'R': // Resposta
            case 'RE': // Resposta e Encerramento
                if ($dadosRecebidos['ds_tipo_resposta'] == 1) {
                    $tramitado = 1;
                    $observacao = "Sua manifestação foi enviada para o setor que irá analisar e responder.";
                } elseif ($dadosRecebidos['ds_tipo_resposta'] == 2 || $dadosRecebidos['ds_tipo_resposta'] == 4) {
                    $respondido = 1;
                    $botaoAcessar = "<a href='" . base_url() . "/Manifestacoes/acessarResposta/$idManifesto' target='_blank' class='btn btn-primary'>Acessar</a>";
                    $observacao = ($dadosRecebidos['ds_tipo_resposta'] == 4) 
                        ? "Sua Contestação foi respondida pela equipe da Ouvidoria. Favor acessar a resposta:" 
                        : "Manifestação respondida pela equipe da Ouvidoria. Favor acessar a resposta:";
                }
                if ($tipoAndamento == 'RE') {
                    $encerrado = 1;
                    $observacao = "Manifestação respondida e encerrada pela equipe da Ouvidoria.";
                }
                $idResposta = $idResposta ?? "";
                break;

            case 'T': // Tramitando
                $idResposta = "";
                $observacao = "Sua manifestação foi encaminhada para o setor responsável.";
                $encerrado = 0;
                $tramitado = 1;
                $respondido = 0;
                break;

            case 'C': // Contestação
                $idResposta = "";
                $observacao = "Manifestação contestada pelo cidadão.";
                $encerrado = 0;
                $tramitado = 0;
                $respondido = 0;
                break;
        }

        // Criar novo andamento
        $dadosAndamento = [
            'fk_id_manifesto' => $dadosRecebidos['fk_id_manifesto'],
            'fk_id_resposta' => $idResposta,
            'ds_data_analise' => date('Y-m-d'),
            'ds_hora_analise' => date('H:i:s'),
            'ds_observacao' => $observacao,
            'ds_status' => 1,
            'ds_tramitado' => $tramitado,
            'ds_respondido' => $respondido,
            'ds_contestado' => 0,
            'ds_encerrado' => $encerrado,
            'ds_agente_ouvidoria' => session('id'),
        ];

        // Inserir andamento e atualizar manifestação
        if ($andamentoManifesto->insert($dadosAndamento)) {
            $manifesto = new ManifestacoesModel();
            $idAndamento = $andamentoManifesto->insertID();

            $manifesto->update($idManifesto, ['ds_andamento_atual' => $idAndamento]);

            return redirect()->route('Manifestacoes')->with('success', 'Manifestação respondida com sucesso.');
        } else {
            return redirect()->route('Manifestacoes')->with('error', $andamentoManifesto->errors());
        }
    }
}
