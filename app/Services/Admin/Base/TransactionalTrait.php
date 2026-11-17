<?php

namespace App\Services\Admin\Base;

use CodeIgniter\Database\BaseConnection;
use App\Exceptions\Admin\DomainException;
use RuntimeException;
use Throwable;

trait TransactionalTrait
{
    protected BaseConnection $db;

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

        } catch (DomainException $e) {
            $this->db->transRollback();
            throw $e;
        } catch (Throwable $e) {
            $this->db->transRollback();
            throw new RuntimeException(
                'Falha na transação: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }
}
