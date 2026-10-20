<?php
/**
 * Mock Login - Simula autenticação Gov.BR para desenvolvimento local.
 *
 * URL: /mock_login?key=4455456
 *
 * Configuração via .env:
 *   MOCK_LOGIN_ATIVO=true
 *   MOCK_LOGIN_SENHA=4455456
 *   MOCK_LOGIN_CPF=12345678900
 *   MOCK_LOGIN_NOME=Desenvolvedor Teste
 *   MOCK_LOGIN_EMAIL=dev@teste.local
 *   MOCK_LOGIN_ID_GOV=999999
 *   MOCK_LOGIN_REDIRECT=Cadastros
 */

namespace App\Controllers\Publica;

use App\Controllers\BaseController;

class MockLogin extends BaseController
{
    public function index()
    {
        $ativo         = env('MOCK_LOGIN_ATIVO', true);
        $senhaEsperada = env('MOCK_LOGIN_SENHA', '4455456');

        if (!$ativo) {
            return $this->response->setStatusCode(403)->setBody('Mock login desativado.');
        }

        $senha = $this->request->getGet('key') ?? '';

        if ($senha !== $senhaEsperada) {
            return $this->response->setStatusCode(401)->setBody('Senha inválida. Use: /mock_login?key=' . $senhaEsperada);
        }

        $cpf      = env('MOCK_LOGIN_CPF', '12345678900');
        $nome     = env('MOCK_LOGIN_NOME', 'Desenvolvedor Teste');
        $email    = env('MOCK_LOGIN_EMAIL', 'dev@teste.local');
        $idGov    = env('MOCK_LOGIN_ID_GOV', '999999');
        $redirect = env('MOCK_LOGIN_REDIRECT', 'Cadastros');

        // Cria sessão exatamente como Gov.BR faria
        session()->set([
            'su'        => 'mock_' . bin2hex(random_bytes(4)),
            'id'        => $idGov,
            'email'     => $email,
            'nome'      => $nome,
            'cpf'       => $cpf,
            'logged_in' => true,
        ]);

        return redirect()->to($redirect);
    }
}
