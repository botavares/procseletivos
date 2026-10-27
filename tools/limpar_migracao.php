<?php
// Limpa diretórios criados pela migração falha
$dirs = [
    'app/Controllers/Admin',
    'app/Models/Admin',
    'app/Services/Admin',
    'app/Views/Admin',
    'app/Repositories/Admin',
    'app/Enums/Admin',
    'app/Exceptions/Admin',
    'app/Traits/Admin',
    'app/Commands/Admin',
    'app/Helpers/Admin',
];

function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir . "/" . $object)) {
                    rrmdir($dir . "/" . $object);
                } else {
                    unlink($dir . "/" . $object);
                }
            }
        }
        rmdir($dir);
    }
}

foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        rrmdir($dir);
        echo "Removido: $dir\n";
    }
}

echo "Limpeza concluída.\n";
