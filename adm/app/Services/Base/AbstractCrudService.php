<?php
namespace App\Services\Base;

use CodeIgniter\Database\BaseConnection;
use RuntimeException;
use Config\Database;
use Throwable;

abstract class AbstractCrudService
{
    protected BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    protected function transactional(callable $callback)
{
    $this->db->transBegin();

    try {

        $result = $callback();

        if ($this->db->transStatus() === false) {

            $error = $this->db->error();

            $message = !empty($error['message'])
                ? $error['message']
                : 'Transação marcada como falha sem erro SQL explícito.';

            throw new RuntimeException(
                'Erro SQL [' . ($error['code'] ?? '0') . '] - ' . $message
            );
        }

        $this->db->transCommit();
        return $result;

    } catch (\Throwable $e) {

        $this->db->transRollback();

        throw new RuntimeException(
            'Falha na transação: ' . $e->getMessage(),
            0,
            $e
        );
    }
}
}
