<?php
namespace App\Services;

use App\Services\Base\AbstractCrudService;
use App\Services\PdfService;
use \App\Models\CargosModel;
use \App\Models\CandidatosModel;
use \App\Models\ProtocolosModel;
use \App\Models\EditaisModel;

class ComprovanteService extends AbstractCrudService{
    protected CandidatosModel $modelCandidatos;
    protected CargosModel $modelCargos;
    protected ProtocolosModel $modelProtocolos;
    protected EditaisModel $modelEditais;
    protected PdfService $pdfService;

    public function __construct(){
        $this->modelCandidatos = new CandidatosModel();
        $this->modelCargos = new CargosModel();
        $this->modelProtocolos = new ProtocolosModel();
        $this->modelEditais = new EditaisModel();
        $this->pdfService = new PdfService();
    }

    /**
     * Gera o comprovante completo com conteúdo PDF, nome do arquivo e tipo MIME.
     * Retorna um objeto padronizado para o controller apenas entregar a resposta.
     */
    public function gerarComprovanteCompleto(int $idEdital, int $idCargo, int $idCandidato): object
    {
        $dadosComprovante = $this->obterDadosComprovante($idEdital, $idCargo, $idCandidato);

        if ($dadosComprovante === null) {
            return (object) [
                'conteudo'    => null,
                'nomeArquivo' => null,
                'tipoMime'    => null,
                'erro'        => 'Protocolo não encontrado para este candidato, cargo e edital.',
            ];
        }

        $pdfContent = $this->pdfService->gerarPdf($dadosComprovante, 'comprovante');

        return (object) [
            'conteudo'    => $pdfContent,
            'nomeArquivo' => "comprovante_candidato_{$idCandidato}.pdf",
            'tipoMime'    => 'application/pdf',
            'erro'        => null,
        ];
    }

    /**
     * Busca e formata os dados necessários para o comprovante.
     * Retorna null se o protocolo não for encontrado.
     */
    private function obterDadosComprovante(int $idEdital, int $idCargo, int $idCandidato): ?array
    {
        $dadosCandidatos = $this->modelCandidatos->where('pk_id_cadastrado', $idCandidato)->first();
        $nomeCargo       = $this->modelCargos->where('pk_id_cargo', $idCargo)->select('ds_nome_cargo')->first();
        $dadosEdital     = $this->modelEditais->where('pk_id_edital', $idEdital)->first();
        $dadosProtocolos = $this->modelProtocolos
            ->where('fk_id_cadastrado', $idCandidato)
            ->where('fk_id_cargo', $idCargo)
            ->where('fk_id_edital', $idEdital)
            ->first();

        if (!$dadosProtocolos) {
            return null;
        }

        $def = $this->pdfService->formatarDeficiencia(
            $dadosCandidatos->fk_id_pne ?? null,
            $dadosCandidatos->ds_outra_pne ?? null
        );

        return [
            'brasao'       => imageToBase64(ROOTPATH . '/external/img/brasao.png'),
            'fundo'        => imageToBase64(ROOTPATH . '/external/img/fundo.jpg'),
            'nomeCargo'    => $nomeCargo->ds_nome_cargo ?? null,
            'edital'       => $this->formatarNumeroEdital($dadosEdital->ds_numero_edital ?? ''),
            'protocolo'    => $dadosProtocolos->ds_protocolo,
            'dadosPessoais'=> $dadosCandidatos,
            'nascimento'   => date('d/m/Y', strtotime($dadosCandidatos->ds_nascimento)),
            'deficiencia'  => $def,
            'dataCadastro' => date('d/m/Y', strtotime($dadosProtocolos->ds_data_protocolo)),
            'horaCadastro' => date('H:i:s', strtotime($dadosProtocolos->ds_hora_protocolo)),
        ];
    }

    private function formatarNumeroEdital(string $numero): string
    {
        $ano = substr($numero, -4);
        $num = ltrim(substr($numero, 0, -4), "0");
        return "Edital {$num}/{$ano}";
    }
}
