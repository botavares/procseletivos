<?php
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
require_once SYSTEMPATH . 'Config/DotEnv.php';
(new CodeIgniter\Config\DotEnv(ROOTPATH))->load();

$db = \Config\Database::connect();
$r = $db->query('SHOW COLUMNS FROM tb_cargos_criterios_adicionais');
foreach ($r->getResultArray() as $row) {
    echo $row['Field'] . ' | ' . $row['Type'] . "\n";
}
