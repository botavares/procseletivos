<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\GovBrUsuarioDTO;
use App\Services\Interfaces\IGovBrService;
use CodeIgniter\HTTP\CURLRequest;

class GovBrService implements IGovBrService
{
    private string $appKey;
    private string $appSecret;
    private CURLRequest $client;

    public function __construct()
    {
        $this->appKey = env('GOVBR_APP_KEY', '');
        $this->appSecret = env('GOVBR_APP_SECRET', '');
        $this->client = service('curlrequest');
    }

    public function autenticar(string $user): GovBrUsuarioDTO
    {
        $data = [
            'su' => $user,
            'ak' => $this->appKey,
            'as' => $this->appSecret,
        ];

        $url = "https://app.prefeituradivinopolis.com.br/app-valid?" . http_build_query($data);

        try {
            $response = $this->client->request('GET', $url, [
                'timeout' => 30,
                'verify' => true,
            ]);
        } catch (\Exception $e) {
            return GovBrUsuarioDTO::erro("Erro na conexão: {$e->getMessage()}");
        }

        $httpCode = $response->getStatusCode();

        if ($httpCode !== 200) {
            return GovBrUsuarioDTO::erro("API retornou código HTTP: {$httpCode}");
        }

        $body = $response->getBody();
        $dados = json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return GovBrUsuarioDTO::erro('Resposta inválida da API: ' . json_last_error_msg());
        }

        if (!isset($dados->usuario)) {
            return GovBrUsuarioDTO::erro('Usuário não encontrado na resposta da API');
        }

        return GovBrUsuarioDTO::sucesso($dados->usuario);
    }

    public function prepararDadosSessao(object $usuario, string $userCode): array
    {
        return [
            'su'        => $userCode,
            'id'        => $usuario->id,
            'email'     => $usuario->email_govbr,
            'nome'      => $usuario->nome,
            'cpf'       => $usuario->cpf,
            'logged_in' => true,
        ];
    }
}
