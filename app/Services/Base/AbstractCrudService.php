<?php
namespace App\Services\Base;

use CodeIgniter\Database\BaseConnection;
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
                throw new \RuntimeException('Falha na transação');
            }

            $this->db->transCommit();
            return $result;

        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }
}
