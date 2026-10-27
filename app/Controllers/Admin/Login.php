<?php

namespace App\Controllers\Admin;

use App\Models\Admin\UsuarioModel;

class Login extends BaseController{
    
    protected $usuarioModel;

    public function __construct(){
        $this->usuarioModel = new \App\Models\Admin\UsuarioModel();
    }
    public function index(){
        $parametros = [
            'camada1'   =>  '',
            'camada2'   =>  'pages',
            'pagina'    =>  'Capa',
            'titulo'    =>  ucfirst('Serviços Divinópolis '.date('Y')),
            'dataAtual' =>  date('d/m/Y'),
        ];
        return view('Admin/layoutLogin', $parametros);
    }

    public function acesso(){

        if($this->request->getMethod() === 'post'){
           $check = $this->usuarioModel->checkPassword(
                $this->request->getPost('user'),
                $this->request->getPost('senha')
            );
           if(!$check){
            return redirect()->route('home')->with('error','Usuário ||| ou senha incorreto!'.$check);
            
           }else{
                $data = [
                    'id'               => $check->pk_id_user,
                    'nome'             => $check->ds_nome_user,
                    'nick'             => $check->ds_nick_user,
                    'perfil'           => $check->ds_perfil_user,
                    'email'            => $check->ds_email_user,
                    'status'           => $check->ds_status_user,
                    'administrador'    => $check->ds_administrador,
                    'fk_id_secretaria' => $check->fk_id_secretaria,
                   
                    'responde'      => $check->ds_responde_recursos,
                    'alteraResposta'=> $check->ds_altera_resposta_manifesto,
                    'tramita'       => $check->ds_tramita_manifesto,
                    'alteratramite' => $check->ds_altera_tramite_manifesto,
                    'encerra'       => $check->ds_encerra_manifesto,
                    'logged_in'     => true
                ];
                $this->session->set($data);
                $this->makeLog->info('ACESSO REALIZADO: '.$data['nick'].' - '.'Perfil: '.$data['perfil'].' - '.date('d/m/Y H:i:s').'-'.'IP: '.$this->request->getIPAddress());
                return redirect()->route('Dashboard');
            }
        }
    }
    public function alterarSenha(){
        $session = session();

        // Proteção: exige login ativo
        if (!$session->has('logged_in')) {
            return redirect('Login.formAlterarSenha')->with('mensagemError', 'Sessão expirada. Faça login novamente.');
        }

        // Captura inputs
        $senhaAtual = $this->request->getPost('senhaAtual');
        $senhaNova  = $this->request->getPost('senhaNova');

        // Validação básica
        if (empty($senhaAtual) || empty($senhaNova)) {
            return redirect('Login.formAlterarSenha')->with('mensagemError', 'Preencha todos os campos.');
        }

        if (strlen($senhaNova) < 6) {
            return redirect('Login.formAlterarSenha')->with('mensagemError', 'A nova senha deve ter no mínimo 6 caracteres.');
        }

        // Usuário logado
        $usuarioLogado = $session->get('id');
        
        // Busca usuário no banco
        $usuario = $this->usuarioModel->find($usuarioLogado);
        
        if (!$usuario) {
            return redirect('Login.formAlterarSenha')->with('mensagemError', 'Usuário não encontrado.');
        }

        // Verifica senha atual
        if (!password_verify($senhaAtual, $usuario->ds_senha_user)) {
            return redirect('Login.formAlterarSenha')->with('mensagemError', 'Senha atual incorreta.');
        }

        // Evita reutilizar senha antiga
        if (password_verify($senhaNova, $usuario->ds_senha_user)) {
            return redirect('Login.formAlterarSenha')->with('mensagemError', 'A nova senha não pode ser igual à anterior.');
        }

       $this->usuarioModel->atualizarSenha($usuarioLogado, $senhaNova);

        return redirect('Login.formAlterarSenha')->with('mensagemSuccess', 'Senha alterada com sucesso.');
    }
    public function formAlterarSenha($camada1 = 'pages',$camada2 = 'alteracoes', $page = 'formAlterarSenha'){
        $params = array(
            'camada1'       => $camada1,
            'camada2'       => $camada2,
            'pagina'        => $page,
            
            'titulo'        => 'Alterar Senha',
            'user'          => session('nome'),
        );
        return view('Admin/layoutDash', $params);
    }
    public function logOut(){
        $this->makeLog->info('LOGOUT: '.session('nick').' - '.'Perfil: '.session('perfil').' - '.date('d/m/Y H:i:s').'-'.'IP: '.$this->request->getIPAddress());
        $this->session->remove('logged_in');
        session()->destroy();
        return redirect()->route('login');
    }
}