<?php

namespace App\Services\Endereco;

class CepService
{
    private string $url = 'https://viacep.com.br/ws/';

    public function consultar(string $cep): array
    {
        $cep = preg_replace('/\D/', '', $cep);

        if (strlen($cep) !== 8) {
            return [
                'success' => false,
                'message' => 'CEP inválido'
            ];
        }

        $response = @file_get_contents($this->url . $cep . '/json/');

        if ($response === false) {
            return [
                'success' => false,
                'message' => 'Erro ao consultar o serviço de CEP'
            ];
        }

        $dados = json_decode($response, true);

        if (isset($dados['erro'])) {
            return [
                'success' => false,
                'message' => 'CEP não encontrado'
            ];
        }

        return [
            'success' => true,
            'data' => [
                'logradouro' => $dados['logradouro'] ?? '',
                'bairro'     => $dados['bairro'] ?? '',
                'cidade'     => $dados['localidade'] ?? '',
                'uf'         => $dados['uf'] ?? ''
            ]
        ];
    }
}
