<?php
namespace App\Services\Editais;

use App\Services\Base\AbstractCrudService;
use App\Models\EditaisModel;
use Exception;

class EditalService extends AbstractCrudService{
     public function __construct(){
        parent::__construct();
    }
    public function salvar(array $edital, array $relacoes): int
    {
        return $this->transactional(function () use ($edital, $relacoes) {

            $model = new EditaisModel();

            if (! $model->save($edital)) {
                throw new \RuntimeException(
                    'Erro ao salvar edital: ' . implode('; ', $model->errors())
                );
            }

            $id = $edital['pk_id_edital'] ?? $model->getInsertID();

            if (empty($id)) {
                throw new \RuntimeException('ID do edital não foi gerado');
            }
            $this->salvarRelacoes($id, $relacoes);

            return $id;
        });
    }

    public function atualizar(array $edital, array $relacoes): void{
        
        $db = db_connect();
        $db->transStart();

        try {
            $model = new EditaisModel();

            $id = $edital['pk_id_edital'] ?? null;

            if (!$id) {
                throw new Exception('ID do edital não informado');
            }

            $isEdital = $model
            ->where('ds_numero_edital', $edital['ds_numero_edital'])
            ->where('pk_id_edital !=', $id)
            ->first();

            if ($isEdital) {
                throw new \RuntimeException('Número do edital já existe');
            }

            // Atualiza o edital
            $model->update($id, $edital);

            // Atualiza relações cursos/abrangências
            $relTable = $db->table('tb_editais_cursos');

            // Remove antigos
            $relTable->where('fk_id_edital', $id)->delete();

            // Insere novos
            foreach ($relacoes['itens'] as $item) {
                $relTable->insert([
                    'fk_id_edital' => $id,
                    $relacoes['modo'] === '1'
                        ? 'fk_id_abrangencia'
                        : 'fk_id_curso' => $item,
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new Exception('Erro ao atualizar edital');
            }

        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }
    }

    private function salvarRelacoes(int $id, array $relacoes): void
    {
        $db = db_connect();

        if ($relacoes['modo'] === '1') {

            $db->table('tb_editais_abrangencias')
               ->where('fk_id_edital', $id)
               ->delete();

            $db->table('tb_editais_abrangencias')
               ->insertBatch(
                   array_map(fn($a) => [
                       'fk_id_edital'      => $id,
                       'fk_id_abrangencia' => $a
                   ], $relacoes['itens'])
               );

        } else {

            $db->table('tb_editais_cursos')
               ->where('fk_id_edital', $id)
               ->delete();

            $db->table('tb_editais_cursos')
               ->insertBatch(
                   array_map(fn($c) => [
                       'fk_id_edital' => $id,
                       'fk_id_curso'  => $c
                   ], $relacoes['itens'])
               );
        }
    }

    public function listarEditalId(int $id){
        $model = new EditaisModel();
        return $model->find($id);
    }

    public function deletar(int $id): void{
        $this->transactional(function () use ($id) {

        $db = $this->db;

        $db->table('tb_editais_cursos')
           ->where('fk_id_edital', $id)
           ->delete();

        $db->table('tb_editais_abrangencias')
           ->where('fk_id_edital', $id)
           ->delete();

        $model = new EditaisModel();

        if (! $model->delete($id)) {
            throw new \RuntimeException('Erro ao excluir o edital');
        }
    });
}


}
