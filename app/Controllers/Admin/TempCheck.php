<?php
namespace App\Controllers\Admin;

class TempCheck extends BaseController {
    public function index() {
        $db = \Config\Database::connect();
        
        try {
            // Verificar se tabelas existem
            $tables = $db->listTables();
            
            echo "Tabelas encontradas:\n";
            foreach ($tables as $table) {
                if (strpos($table, 'cargos') !== false || strpos($table, 'experiencia') !== false) {
                    echo "- {$table}\n";
                }
            }
            
            echo "\n\nVerificando tb_cargos_experiencias:\n";
            if (in_array('tb_cargos_experiencias', $tables)) {
                echo "TABELA EXISTE\n";
                $fields = $db->getFieldData('tb_cargos_experiencias');
                foreach ($fields as $field) {
                    echo "  - {$field->name}\n";
                }
            } else {
                echo "TABELA NÃO EXISTE!\n";
            }
            
            echo "\n\nVerificando tb_experiencias:\n";
            if (in_array('tb_experiencias', $tables)) {
                echo "TABELA EXISTE\n";
                $fields = $db->getFieldData('tb_experiencias');
                foreach ($fields as $field) {
                    echo "  - {$field->name}\n";
                }
            } else {
                echo "TABELA NÃO EXISTE!\n";
            }
            
        } catch (\Exception $e) {
            echo "ERRO: " . $e->getMessage();
        }
    }
}
