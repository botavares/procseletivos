<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameSituacaoToNmIdSituacaoTbSituacaoCandidato extends Migration
{
    public function up(): void
    {
        $this->db->query("
            ALTER TABLE `tb_situacao_candidato`
            CHANGE `situacao` `nm_id_situacao` INT(11) NOT NULL
        ");
    }

    public function down(): void
    {
        $this->db->query("
            ALTER TABLE `tb_situacao_candidato`
            CHANGE `nm_id_situacao` `situacao` INT(11) NOT NULL
        ");
    }
}
