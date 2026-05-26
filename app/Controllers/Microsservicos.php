<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
use Dompdf\Dompdf;
use chillerlan\QRCode\{QRCode, QROptions};
use App\Modules\Breadcrumbs\Breadcrumbs;



    class Microsservicos extends BaseController{
        protected $breadcrumbs;
    public function __construct(){
       /* $this->breadcrumbs = new Breadcrumbs();
        $this->breadcrumb = [
            'breads' => $this->breadcrumbs->buildAuto(),
        ];
		*/
    }
    public function index($camada1 = '', $camada2 = 'pages', $page = 'Servicos'){

        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }
        //Destaques
        $servicosModel    =   new \App\Models\ServicosModel();
        $categoriasModel    =   new \App\Models\CategoriasServicosModel();

        //Mais Acessados
        
        $destaques          =   $servicosModel->where('ds_destaque',1)->orderBy('ds_nome_servico', 'asc')->findAll();
        $servicos           =   $servicosModel->where('ds_status >=',1)->orderBy('ds_nome_servico', 'asc')->findAll();
        $categoriaServicos  =   $categoriasModel->where('ds_status',1)->findAll();
        $contarServicos     =   $servicosModel->where('ds_status >=',1)->countAllResults();

        $parametros = [
            'camada1'       =>  $camada1,
            'camada2'       =>  $camada2,
            'pagina'        =>  $page,
            'titulo'        =>  ucfirst('Governo Digital'),
            'servicos'      =>  $servicos,
            //'breads'        =>  $this->breadcrumb['breads'],
            'categorias'    =>  $categoriaServicos,
            'contagemServicos'  =>  $contarServicos,
            'dataAtual'     =>  date('d/m/Y'),
        ];
        echo view('layoutSimplesHeader2',$parametros);
    }

    public function redirServicos($idServico){
        $servicosModel = new \App\Models\ServicosModel();
        $contagemClicks  = $servicosModel->select('ds_total_clicks,ds_link_servico,ds_possui_instrucao')->where('pk_id_servico',$idServico)->first();
        $somaClicks = $contagemClicks->ds_total_clicks + 1;
        $servicosModel->set('ds_total_clicks', $somaClicks)->where('pk_id_servico', $idServico)->update();

        if($contagemClicks->ds_possui_instrucao == 1){
            $url = base_url('Microsservicos/redirInstrucoes/'.$idServico);
            return redirect()->to($url);
        }else{
            $url = "$contagemClicks->ds_link_servico";
            return redirect()->to($url);
        }
        
    }
		
		
    public function redirInstrucoes($idServico){
        $servicosModel = new \App\Models\InstrucoesModel();

        $url = base_url('Microsservicos/instrucao/'.$idServico);
        return redirect()->to($url);
    }

		
		
		
		
    public function instrucao($idServico,$camada1 = '', $camada2 = 'pages', $page = 'Instrucao'){

        if (! is_file(APPPATH . 'Views/'.$camada1.'/'.$camada2.'/'. $page . '_view.php')) {
            // Página não encontrada!
            throw new PageNotFoundException("página não econtrada: ".$page);
        }
   
        $servicosModel    	=   new \App\Models\ServicosModel();
        $instrucoesModel    =   new \App\Models\InstrucoesModel();
		$gruposModel		=	new \App\Models\GrupoSolicitacoesModel();
		
        $instrucao          =	$instrucoesModel->where('fk_id_servico',$idServico)->orderBy('pk_id_instrucao', 'asc')->findAll();
        $servicos           =	$servicosModel->where('pk_id_servico',$idServico)->orderBy('ds_nome_servico', 'asc')->first();
		if($instrucao[0]->fk_id_gruposolicitacao > 0){
			$grupos	=	$gruposModel->where('pk_id_gruposolicitacao',$instrucao[0]->fk_id_gruposolicitacao)->first();
			$descricaoGrupo = $grupos->ds_nome_grupo_solicitacao;
		}else{
			$descricaoGrupo = "";
		}


        $parametros = [
            'camada1'       =>  $camada1,
            'camada2'       =>  $camada2,
            'pagina'        =>  $page,
            'titulo'        =>  ucfirst('Governo Digital'),
            'servico'       =>  $servicos,
			'grupos'		=>	$descricaoGrupo,
            'instrucao'     =>  $instrucao,
            'dataInstrucao'     =>  ($instrucao[0]->ds_data_alteracao)?date('d/m/Y', strtotime($instrucao[0]->ds_data_alteracao)):date('d/m/Y'),
        ];
        echo view('layoutSimplesHeader2',$parametros);
    }
    

}
