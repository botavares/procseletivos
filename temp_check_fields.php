<?php
require 'C:\laragon\www\servicos\procseletivos\adm\vendor\autoload.php';
$db = Config\Database::connect();
$fields = $db->getFieldData('tb_cargos_experiencias_editais');
foreach ($fields as $f) {
    echo $f->name . ' | ' . $f->type . ' | ' . ($f->nullable ? 'NULL' : 'NOT NULL') . ' | default: ' . ($f->default ?? 'none') . PHP_EOL;
}
