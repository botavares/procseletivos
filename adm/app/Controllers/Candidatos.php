<?php

namespace App\Controllers;
use FilesystemIterator;
use Dompdf\Dompdf;
use Dompdf\Options;
use chillerlan\QRCode\{QRCode, QROptions};
use CodeIgniter\Exceptions\PageNotFoundException;

use App\DTOs\Domain\Candidatos\SituacaoDTO;
use App\DTOs\Domain\Candidatos\SituacaoDetalhadaDTO;

use App\Services\LogsService;
use App\Services\EmailService;
use App\Services\Candidatos\CandidatosService;
use App\Services\Candidatos\SituacaoService;

use App\Models\EditaisModel;
use App\Models\CargosModel;
use App\Models\VagasModel;
use App\Models\InstituicoesModel;
use App\Models\EditaisCargosModel;
use App\Models\EditaisCandidatosModel;
use App\Models\DadosContratosModel;
use App\Models\CandidatosModel;
use App\Models\CursosModel;

use App\Modules\Breadcrumbs\Breadcrumbs;

//traits
use App\Traits\AtualizarAndamentoTrait;
use App\Traits\RegistrarEventoTrait;

// services
use App\Services\ContagemDeTempoService;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

use CodeIgniter\Controller;

class Candidatos extends BaseController{

    use AtualizarAndamentoTrait;
    use RegistrarEventoTrait;
    protected $manifestosData;
    

    public function __construct(){
      
      
    }

    /*==============================================================================
        FUNÇÃO: index;
        OBJETIVO: O index dessa classe será utilizada para abrir a tela inicial de candidatos;
        PARAMETROS: camada1 = primeira pasta após pasta views, camada2 = segunda pasta, $page = nome da view
        CRIAÇÃO:25/09/2025
        MODIFICADO:
        RESUMO: Função abre o tela inicial de cadidatos. 
        ==============================================================================*/
    public function index($idEdital = null, $idCargo = null, $camada1 = 'pages', $camada2 = 'candidatos', $page = 'Candidatos'){
        if (!is_file(APPPATH . 'Views/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }

        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $modelEditais   = new EditaisModel();
        $modelCargos    = new CargosModel();
        $modelCandidato = new CandidatosModel();
        $contratosModel  = new DadosContratosModel();

        if (!$idEdital || !$idCargo) {
            // Se não vier na URL, poderia redirecionar de volta
            return redirect()->to(base_url('home'));
        }
        $dadosCargo = $modelCargos->getCargo($idCargo);

        $candidatos = $modelCandidato->getCandidatosPorEditalCargo($idEdital, $idCargo);
        
        $dadosEdital = $modelEditais->getEdital($idEdital);

         if(!$dadosCargo || !$dadosEdital){
            return redirect()->to(base_url('home'))->with('error', 'Cargo ou Edital não encontrado.');
         }

         // Para cada candidato, calcular os dias trabalhados   
        
        $arrayCandidatos = [];
        foreach($candidatos as $candidato){
            $arrayCandidatos[$candidato->pk_id_cadastrado]["pk_id_cadastrado"] = $candidato->pk_id_cadastrado;
            $arrayCandidatos[$candidato->pk_id_cadastrado]["ds_numero_edital"] = $candidato->ds_numero_edital;
            $arrayCandidatos[$candidato->pk_id_cadastrado]["ds_data_cadastro"] = $candidato->ds_data_cadastro;
            $arrayCandidatos[$candidato->pk_id_cadastrado]["ds_nome"] = $candidato->ds_nome;
            $arrayCandidatos[$candidato->pk_id_cadastrado]["ds_nascimento"] = $candidato->ds_nascimento;
            $arrayCandidatos[$candidato->pk_id_cadastrado]["ds_celular"] = $candidato->ds_celular;
            $arrayCandidatos[$candidato->pk_id_cadastrado]["ds_email"] = $candidato->ds_email;
            $arrayCandidatos[$candidato->pk_id_cadastrado]["fk_id_edital"] = $idEdital;
            $arrayCandidatos[$candidato->pk_id_cadastrado]["fk_id_cargo"] = $idCargo;
            $arrayCandidatos[$candidato->pk_id_cadastrado]["ds_possui_pne"] = ((int)($candidato->fk_id_pne ?? 0)) > 1 ? 1 : 0;

        }

        $titulosTabela = ["Edital Ref.","Data de Insc.","Nome do Candidato","Nascimento","Telefone","Email","Recursos"];
        
        $parametros = [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'candidatos'    => $arrayCandidatos,
            'idEdital'      => $idEdital,
            'idCargo'       => $idCargo,
            'nomeCargo'     => $dadosCargo->ds_nome_cargo,
            'perfil'        => session('perfil'),
            'user'          => session('nome'),
            "titulosTabela" => $titulosTabela,
            'titulo'        => "Gerenciar Candidatos",
        ];

        echo view('layoutDash', $parametros);
    }
    /*===============================================================================
            FUNÇÃO: salvarEscolha;
            OBJETIVO: Salvar a escolha do edital e curso e redirecionar para a listagem de candidatos;
            PARAMETROS: nenhum
            CRIAÇÃO:25/09/2025
            MODIFICADO:
            RESUMO: Função salva a escolha do edital e curso e redireciona para a listagem de candidatos.
    ==============================================================================*/
    public function salvarEscolha(){
        $idEdital = $this->request->getPost('edital');
        $idCargo  = $this->request->getPost('cargo');

        // redireciona para GET
        return redirect()->to(base_url("Candidatos/{$idEdital}/{$idCargo}"));
    }



    
    public function exibirDados($edital,$cargo,$id, $camada1 = 'pages', $camada2 = 'candidatos', $page = 'dadosCandidato'){
        if (!is_file(APPPATH . 'Views/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }
        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $candidatosService = new CandidatosService();
        $dadosCandidato = $candidatosService->listarCandidatoId($edital,$cargo,$id);
        if(!$dadosCandidato){
            return redirect()->back()->with('error', 'Candidato não encontrado.');
        }
        $parametros = [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'dadosCandidato'     => $dadosCandidato,
            'perfil'        => session('perfil'),
            'user'          => session('nome'),
            'titulo'        => "Dados do Candidato",
        ];

        echo view('layoutDash', $parametros);
    }   

    public function getCandidatosByCargoAndEdital(){
        $idCargo = $this->request->getPost('idCargo');
        $idEdital = $this->request->getPost('idEdital');
        $candidatosModel = new CandidatosModel();
        $candidatos = $candidatosModel->getCandidatosPorEditalCargo($idEdital, $idCargo);
        return $this->response->setJSON($candidatos);
    }

    public function formSituacaoCandidato($edital,$cargo,$id, $camada1 = 'pages', $camada2 = 'candidatos', $page = 'FormSituacaoCandidato'){
        if (!is_file(APPPATH . 'Views/' . $camada1 . '/' . $camada2 . '/' . $page . '_view.php')) {
            throw new PageNotFoundException("Página não encontrada: " . $page);
        }
        if (!checklogged()) {
            return redirect()->route('home')->with('error','Sua sessão expirou');
        }

        $candidatosService = new CandidatosService();
        $dadosCandidato = $candidatosService->listarCandidatoId($edital,$cargo,$id);
        if(!$dadosCandidato){
            return redirect()->back()->with('error', 'Candidato não encontrado.');
        }
        
        $nomeCandidato = $dadosCandidato["candidato"]->ds_nome;
        $idCandidato = $dadosCandidato["idCandidato"];
        $idEdital = $dadosCandidato["idEdital"];
        $idCargo = $dadosCandidato["idCargo"];
        $cargosModel = new CargosModel();
        $nomeCargo = $cargosModel->where('pk_id_cargo', $idCargo)->first()->ds_nome_cargo;
        
        $situacaoService = service('situacaoService');
        $dadosSituacao = $situacaoService->getCandidatoCargoEdital($idEdital,$idCargo,$idCandidato);
        $dadosSituacao ??= new SituacaoDTO();
        
        $parametros = [
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            'candidato'     => $nomeCandidato,
            'idCandidato'   => $idCandidato,
            'idEdital'      => $idEdital,
            'idCargo'       => $idCargo,
            'nomeCargo'     => $nomeCargo,
            'dadosSituacao' => $dadosSituacao,
            'perfil'        => session('perfil'),
            'user'          => session('nome'),
            'titulo'        => "Situação do Candidato",
        ];

        echo view('layoutDash', $parametros);
        
    }
    public function registrarSituacao(){
        $idCandidato = $this->request->getPost('id_candidato');
        $idCargo = $this->request->getPost('id_cargo');
        $idEdital = $this->request->getPost('id_edital');
        $situacao = $this->request->getPost('situacao');

        $situacaoDTO = SituacaoDTO::fromArray([
            'fk_id_candidato' => $idCandidato,
            'fk_id_cargo' => $idCargo,
            'fk_id_edital' => $idEdital,
            'situacao' => $situacao
        ]);

        $situacaoService = service('situacaoService');
        $dadosSituacaoExistente = $situacaoService->getCandidatoCargoEdital($idEdital,$idCargo,$idCandidato);
        
        if($dadosSituacaoExistente !== null){
            if($situacao > 0){
                //atualizar
                $situacaoService->atualizar($dadosSituacaoExistente->id, $situacaoDTO);
                return redirect()->back()->with('mensagemSuccess', 'Situação atualizada com sucesso.');
            }else{
                //excluir
                $situacaoService->excluir($dadosSituacaoExistente->id);
                return redirect()->back()->with('mensagemSuccess', 'Situação excluída com sucesso.');
            }
            
        }else{
            //salvar
            $situacaoService->salvar($situacaoDTO);
            return redirect()->back()->with('mensagemSuccess', 'Situação registrada com sucesso.');
        }
    }

    /**
     * Exporta lista de candidatos para Excel (.xlsx)
     * com opção de filtrar apenas PCD.
     */
    public function exportarXlsx($edital, $cargo)
    {
        $modelEditais   = new EditaisModel();
        $modelCargos    = new CargosModel();
        $modelCandidato = new CandidatosModel();

        $dadosEdital = $modelEditais->getEdital($edital);
        $dadosCargo  = $modelCargos->getCargo($cargo);
        $candidatos  = $modelCandidato->getCandidatosPorEditalCargo($edital, $cargo);

        if (empty($candidatos)) {
            return redirect()->back()
                ->with('error', 'Não há candidatos para exportar.');
        }

        // Filtra apenas PCD se solicitado
        $apenasPcd = $this->request->getGet('pcd');
        $somentePcd = ($apenasPcd !== null && $apenasPcd == '1');
        if ($somentePcd) {
            $candidatos = array_filter($candidatos, function ($c) {
                return ((int)($c->fk_id_pne ?? 0)) > 1;
            });
            if (empty($candidatos)) {
                return redirect()->back()
                    ->with('error', 'Não há candidatos PCD nesta listagem.');
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Candidatos');

        $nomeEdital = $dadosEdital->ds_numero_edital ?? 'edital';
        $nomeCargo  = $dadosCargo->ds_nome_cargo   ?? 'cargo';

        // ====== HEADERS ======
        $headers = ['Data de Insc.', 'Nome do Candidato', 'Nascimento', 'Telefone', 'Email'];
        $totalCols = count($headers);
        $ultimaColuna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols);

        // ====== CABEÇALHO INSTITUCIONAL ======
        $sheet->mergeCells("A1:{$ultimaColuna}1");
        $sheet->setCellValue('A1', 'PREFEITURA MUNICIPAL DE DIVINÓPOLIS');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1F4E78');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->mergeCells("A2:{$ultimaColuna}2");
        $sheet->setCellValue('A2', "Edital: {$nomeEdital}  |  Cargo: {$nomeCargo}");
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('305496');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(22);

        $sheet->mergeCells("A3:{$ultimaColuna}3");
        $sheet->setCellValue('A3', 'Listagem gerada em ' . date('d/m/Y H:i:s'));
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('444444'));
        $sheet->getStyle('A3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(20);

        // ====== CABEÇALHO DA TABELA (linha 5) ======
        $linhaCabecalho = 5;
        $col = 1;
        foreach ($headers as $header) {
            $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $linhaCabecalho;
            $sheet->setCellValue($cellCoord, $header);
            $sheet->getStyle($cellCoord)->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
            $sheet->getStyle($cellCoord)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('305496');
            $sheet->getStyle($cellCoord)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($cellCoord)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D9D9D9');
            $col++;
        }
        $sheet->getRowDimension($linhaCabecalho)->setRowHeight(22);

        // ====== DADOS ======
        $row = $linhaCabecalho + 1;
        foreach ($candidatos as $candidato) {
            $col = 1;

            // Data de Insc.
            $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row;
            $sheet->setCellValue($cellCoord, date('d/m/Y', strtotime($candidato->ds_data_cadastro)));
            $this->aplicarEstiloCelulaCandidatos($sheet, $cellCoord, $row);

            // Nome do Candidato
            $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row;
            $sheet->setCellValue($cellCoord, $candidato->ds_nome);
            $this->aplicarEstiloCelulaCandidatos($sheet, $cellCoord, $row, Alignment::HORIZONTAL_LEFT);

            // Nascimento
            $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row;
            $sheet->setCellValue($cellCoord, date('d/m/Y', strtotime($candidato->ds_nascimento)));
            $this->aplicarEstiloCelulaCandidatos($sheet, $cellCoord, $row);

            // Telefone
            $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row;
            $sheet->setCellValue($cellCoord, mask($candidato->ds_celular, '(##) #####-####'));
            $this->aplicarEstiloCelulaCandidatos($sheet, $cellCoord, $row);

            // Email
            $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row;
            $sheet->setCellValue($cellCoord, $candidato->ds_email);
            $this->aplicarEstiloCelulaCandidatos($sheet, $cellCoord, $row, Alignment::HORIZONTAL_LEFT);

            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        // Auto-size
        foreach (range(1, count($headers)) as $colIndex) {
            $sheet->getColumnDimensionByColumn($colIndex)->setAutoSize(true);
        }

        // Nome do arquivo
        $safeEdital = preg_replace('/[^A-Za-z0-9_-]/', '_', $nomeEdital);
        $safeCargo  = preg_replace('/[^A-Za-z0-9_-]/', '_', $nomeCargo);
        $sufixoPcd  = $somentePcd ? '_PCD' : '';
        $fileName   = "Candidatos{$sufixoPcd}_{$safeEdital}_{$safeCargo}_" . date('Ymd_His') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function aplicarEstiloCelulaCandidatos($sheet, string $cellCoord, int $row, string $horizontalAlign = Alignment::HORIZONTAL_CENTER): void
    {
        $sheet->getStyle($cellCoord)->getFont()->setSize(11);
        $sheet->getStyle($cellCoord)->getAlignment()->setHorizontal($horizontalAlign)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($cellCoord)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D9D9D9');

        // Zebra striping
        if ($row % 2 == 0) {
            $sheet->getStyle($cellCoord)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8F9FA');
        } else {
            $sheet->getStyle($cellCoord)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
        }
    }

    
}