<?php

namespace App\Services\Classificacao;

use CodeIgniter\Database\BaseConnection;

/**
 * Responsável por persistir a classificação
 * utilizando transação e insertBatch.
 */
class ClassificacaoPersistService
{
    public function __construct(
        private BaseConnection $db
    ) {}

    public function salvar(int $edital, int $cargo, array $dados): void{
        $this->db->transStart();

        // Delete único
        $this->db->table('tb_classificacao')
            ->where([
                'fk_id_edital' => $edital,
                'fk_id_cargo'  => $cargo
            ])
            ->delete();

        // Ajusta edital/cargo antes de inserir
        foreach ($dados as &$row) {
            $row['fk_id_edital'] = $edital;
            $row['fk_id_cargo']  = $cargo;
        }
        if (empty($dados)) {
            dd('Sem dados para inserir');
        }
        
        // Insert em lote
        $this->db->table('tb_classificacao')
            ->insertBatch($dados);
            
        $this->db->transComplete();
    }
}