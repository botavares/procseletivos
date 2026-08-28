<?php

namespace App\Services\Base;

use App\Contracts\ISessaoService;

/**
 * Serviço para gerenciamento de sessão
 * @package App\Services
 */
class SessaoService implements ISessaoService
{
    /**
     * @inheritDoc
     */
    public function estaLogado(): bool
    {
        return (bool) session('logged_in');
    }

    /**
     * @inheritDoc
     */
    public function obterUsuario(): ?array
    {
        if (!$this->estaLogado()) {
            return null;
        }

        return [
            'su' => session('su'),
            'id' => session('id'),
            'email' => session('email'),
            'nome' => session('nome'),
            'cpf' => session('cpf'),
            'logged_in' => session('logged_in'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function obterCpf(): ?string
    {
        return session('cpf') ?: null;
    }

    /**
     * @inheritDoc
     */
    public function obterIdUsuario(): ?int
    {
        $id = session('id');
        return $id ? (int) $id : null;
    }

    /**
     * @inheritDoc
     */
    public function definirUsuario(array $dados): void
    {
        $session = session();
        $session->set([
            'su' => $dados['su'] ?? '',
            'id' => $dados['id'] ?? null,
            'email' => $dados['email'] ?? '',
            'nome' => $dados['nome'] ?? '',
            'cpf' => $dados['cpf'] ?? '',
            'logged_in' => true,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function definirProtocoloAutenticado(string $protocolo): void
    {
        session()->set('protocolo_autenticado', $protocolo);
    }

    /**
     * @inheritDoc
     */
    public function obterProtocoloAutenticado(): ?string
    {
        return session('protocolo_autenticado') ?: null;
    }

    /**
     * @inheritDoc
     */
    public function temProtocoloAutenticado(): bool
    {
        return (bool) session('protocolo_autenticado');
    }

    /**
     * @inheritDoc
     */
    public function limparProtocoloAutenticado(): void
    {
        session()->remove('protocolo_autenticado');
    }

    /**
     * @inheritDoc
     */
    public function destruir(): void
    {
        $session = session();
        // Limpa todos os dados de sessão primeiro
        $session->set([
            'logged_in' => false,
            'su'        => null,
            'id'        => null,
            'email'     => null,
            'nome'      => null,
            'cpf'       => null,
            'protocolo_autenticado' => null,
            'ultima_atividade'      => null,
        ]);
        // Regenera o ID da sessão para invalidar o cookie antigo
        $session->regenerate(true);
    }

    /**
     * @inheritDoc
     * Obtém o IP do cliente considerando proxy reverso
     * Prioridade: HTTP_X_FORWARDED_FOR > HTTP_X_REAL_IP > HTTP_CLIENT_IP > REMOTE_ADDR
     */
    public function obterIp(): string
    {
        // Verifica HTTP_X_FORWARDED_FOR (proxy reverso - pode conter múltiplos IPs separados por vírgula)
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Pega o primeiro IP da lista (IP real do cliente)
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ips[0]);
            if ($this->isIpValido($ip)) {
                return $ip;
            }
        }
        
        // Verifica HTTP_X_REAL_IP (header comum em proxies Nginx/Apache)
        if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            $ip = trim($_SERVER['HTTP_X_REAL_IP']);
            if ($this->isIpValido($ip)) {
                return $ip;
            }
        }
        
        // Verifica HTTP_CLIENT_IP
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = trim($_SERVER['HTTP_CLIENT_IP']);
            if ($this->isIpValido($ip)) {
                return $ip;
            }
        }
        
        // Fallback para REMOTE_ADDR
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Verifica se um IP é válido
     *
     * @param string $ip
     * @return bool
     */
    private function isIpValido(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * @inheritDoc
     */
    public function registrarAtividade(string $acao): void
    {
        session()->set('ultima_atividade', [
            'acao' => $acao,
            'timestamp' => time(),
        ]);
    }

    /**
     * Obtém o identificador de sessão
     *
     * @return string|null
     */
    public function obterIdSessao(): ?string
    {
        return session_id() ?: null;
    }

    /**
     * Obtém dados completos da sessão
     *
     * @return array
     */
    public function obterTodosDados(): array
    {
        return $_SESSION ?? [];
    }

    /**
     * Define um valor na sessão
     *
     * @param string $chave
     * @param mixed $valor
     * @return void
     */
    public function set(string $chave, $valor): void
    {
        session()->set($chave, $valor);
    }

    /**
     * Obtém um valor da sessão
     *
     * @param string $chave
     * @return mixed
     */
    public function get(string $chave)
    {
        return session($chave);
    }

    /**
     * Remove um valor da sessão
     *
     * @param string $chave
     * @return void
     */
    public function remove(string $chave): void
    {
        session()->remove($chave);
    }
}
