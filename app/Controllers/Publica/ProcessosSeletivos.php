<?php

declare(strict_types=1);

namespace App\Controllers\Publica;

use App\Controllers\BaseController;
use App\Enums\Admin\SituacaoProcessosEnum;
use App\Enums\TipoCargaHorariaEnum;
use App\Services\Base\SessaoService;
use App\Services\ProcessosSeletivos\ProcessosSeletivosService;
use App\Services\ServicoService;
use App\Services\SecretariasService;
use CodeIgniter\Exceptions\PageNotFoundException;

class ProcessosSeletivos extends BaseController
{
    protected ProcessosSeletivosService $processosSeletivosService;
    protected ServicoService $servicoService;
    protected SessaoService $sessaoService;
    protected SecretariasService $secretariasService;

    public function __construct()
    {
        $this->processosSeletivosService = service('processosSeletivosService');
        $this->servicoService = service('servicoService');
        $this->sessaoService = service('sessao');
        $this->secretariasService = service('secretariasService');
    }

    public function index($camada1 = '', $camada2 = 'pages', $page = 'ProcessosSeletivos'){
        if (!is_file(APPPATH . 'Views/Publica/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("página não encontrada: " . $page);
        }

        $layout = $this->sessaoService->estaLogado() ? 'Publica/layoutLogado' : 'Publica/layoutSimples';

        $parametros = [
            'camada1'                   => $camada1,
            'camada2'                   => $camada2,
            'pagina'                    => $page,
            'titulo'                    => ucfirst('Editais Ativos'),
            'dataAtual'                 => date('d/m/Y'),
        ];

        return view($layout, $parametros);
    }

    public function detalhes(int $idProcesso){
        $processo = $this->processosSeletivosService->buscarPorId($idProcesso);
        
        $secretaria = $this->secretariasService->getSecretariaById((int) ($processo->fk_id_secretaria ?? 0));
        
        if (!$processo) {
            throw new PageNotFoundException('Processo seletivo não encontrado.');
        }

        $cargosDoProcesso = $this->processosSeletivosService->buscarCargosPorProcesso($idProcesso);

        // Adiciona label do tipo de carga horária em cada cargo e configura a remuneração formatada
        foreach ($cargosDoProcesso as $cargo) {
            // Adiciona label do tipo de carga horária
            $tipo = TipoCargaHorariaEnum::tryFrom((int) ($cargo->nm_tipo_carga_horaria ?? 0));
            $cargo->ds_tipo_carga_horaria = $tipo?->label() ?? '—';

            // Configura a remuneração formatada
            $remuneracao = $cargo->nm_remuneracao ?? null;
            if ($remuneracao !== null && $remuneracao !== '' && $remuneracao != 0) {
                $cargo->ds_remuneracao = 'R$ ' . number_format((float) $remuneracao, 2, ',', '.');
            } else {
                $cargo->ds_remuneracao = '—';
            }
        }

        // Faz a identificação da situação do processo seletivo e define a classe e o rótulo da tag correspondente
        $situacaoId = (int) ($processo->nm_situacao ?? 0);
        $enum = SituacaoProcessosEnum::tryFrom($situacaoId);
        $tagClass = match ($situacaoId) {
            1, 6 => 'home-tag--aberto',
            4     => 'home-tag--vigente',
            default => 'home-tag--encerrado',
        };
        $tagLabel = $enum?->label() ?? 'Encerrado';

        // Configura o layout e os parâmetros para a view (logado no govbr ou não)
        $layout = $this->sessaoService->estaLogado() ? 'Publica/layoutLogado' : 'Publica/layoutSimples';

        //Calcular validade em data do processo
        $validade = $this->processosSeletivosService->calcularValidade($idProcesso);
        

        
        $parametros = [
            'camada1'       => 'pages',
            'camada2'       => 'ProcessosSeletivos',
            'pagina'        => 'DadosProcessos',
            'titulo'        => 'Detalhes do Processo Seletivo',
            'idProcesso'    => $idProcesso,
            'processo'      => $processo,
            'processoNum'   => ($processo->nm_numero_processo ?? '?') . '/' . ($processo->nm_ano_processo ?? '?'),
            'validade'      => $validade,
            'secretaria'    => $secretaria,
            'objetivo'      => $processo->ds_objeto ?? 'Objetivo não informado.',
            'dataInicial'   => isset($processo->dt_inicio_inscricao) ? date('d/m/Y', strtotime($processo->dt_inicio_inscricao)) : '—',
            'dataTermino'   => isset($processo->dt_fim_inscricao) ? date('d/m/Y', strtotime($processo->dt_fim_inscricao)) : '—',
            'dataHomologacao' => isset($processo->dt_data_homologacao) ? date('d/m/Y', strtotime($processo->dt_data_homologacao)) : '—',
            'situacaoId'    => $situacaoId,
            'tagClass'      => $tagClass,
            'tagLabel'      => $tagLabel,
            'cargos'        => $cargosDoProcesso,
            'resultados'    => [],
            'convocacoes'   => [],
            'contratos'     => [],
            'dataAtual'     => date('d/m/Y'),
        ];

        return view($layout, $parametros);
    }

    public function obterEdital($arquivo)
    {
        $path = ROOTPATH . 'writable/uploads/Editais/' . $arquivo;

        if (!is_file($path)) {
            throw new PageNotFoundException('Arquivo não encontrado.');
        }

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $arquivo . '"')
            ->setBody(file_get_contents($path));
    }

    public function buscarServicosServidores()
    {
        $busca = $this->request->getGet('q');

        if ($busca === null) {
            return $this->response->setJSON([]);
        }

        $dataBusca = $this->servicoService->buscarServicosServidores((string) $busca);

        return $this->response->setJSON($dataBusca);
    }

    public function autenticarProcesso($token)
    {
        $edital = $this->editaisService->autenticarEditalPorToken($token);

        if (!$edital) {
            throw new PageNotFoundException('Edital não encontrado ou token inválido.');
        }

        $parametros = [
            'titulo' => 'Autenticação de Edital',
            'edital' => $edital,
        ];

        return view('Publica/editais/autenticar_edital_view', $parametros);
    }
}
