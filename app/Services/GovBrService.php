<?php
// app/Services/GovBrService.php
namespace App\Services;
class GovBrService
{
    /**
     * Credenciais da API (deveriam vir de .env)
     */
    private array $config;
    public function __construct()
    {
        // Ideal: carregar de variáveis de ambiente
        $this->config = [
            //'ak' => "4536f180bdc0de3b1cf67f3f9a60ea86", // CHAVE DE TESTE
            //'as' => "7762d57af7e926a4909827b337b05d5b", // SECRET DE TESTE
            'ak' => "0b7f390e92176b48bdd12a6488dcd547", // CHAVE DE PROD
            'as' => "04a3ae30dba5b02989d10cb58cd2a9e9", // SECRET DE PROD
        ];
    }
    /**
     * Autentica usuário no Gov.BR
     * 
     * @param string $user Código do usuário
     * @return array ['sucesso' => bool, 'dados' => object|array, 'erro' => string|null]
     */
    public function autenticar(string $user): array
    {
        $data = [
            'su' => $user,
            'ak' => $this->config['ak'],
            'as' => $this->config['as'],
        ];
        $url = "https://app.prefeituradivinopolis.com.br/app-valid?" . http_build_query($data);
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPGET => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return [
                'sucesso' => false,
                'erro' => "Erro na conexão: {$error}",
                'dados' => null
            ];
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode !== 200) {
            return [
                'sucesso' => false,
                'erro' => "API retornou código HTTP: {$httpCode}",
                'dados' => null
            ];
        }
        $dados = json_decode($response);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'sucesso' => false,
                'erro' => 'Resposta inválida da API: ' . json_last_error_msg(),
                'dados' => null
            ];
        }
        // Verifica se o usuário existe na resposta
        if (!isset($dados->usuario)) {
            return [
                'sucesso' => false,
                'erro' => 'Usuário não encontrado na resposta da API',
                'dados' => null
            ];
        }
        return [
            'sucesso' => true,
            'erro' => null,
            'dados' => $dados->usuario
        ];
    }
    /**
     * Formata os dados da API para sessão
     */
    public function prepararDadosSessao(object $usuario, string $userCode): array
    {
        return [
            'su'        => $userCode,
            'id'        => $usuario->id,
            'email'     => $usuario->email_govbr,
            'nome'      => $usuario->nome,
            'cpf'       => $usuario->cpf,
            'logged_in' => true
        ];
    }
}