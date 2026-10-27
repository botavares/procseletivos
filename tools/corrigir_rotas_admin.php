<?php
/**
 * Corrige dupla barra nas rotas do admin dentro do grupo 'adm'
 */

$file = 'app/Config/Routes.php';
$content = file_get_contents($file);

// Localiza o bloco do admin e remove a barra inicial das rotas dentro dele
$lines = explode("\n", $content);
$inAdminBlock = false;
$newLines = [];

foreach ($lines as $line) {
    if (strpos($line, "\$routes->group('adm'") !== false) {
        $inAdminBlock = true;
    }
    
    if ($inAdminBlock && strpos($line, "\$routes->") !== false) {
        $line = preg_replace("/(\$routes->(?:get|post|put|delete|patch|options|head|cli)\s*\(\s*['\"])\//", '\$1', $line);
    }
    
    if ($inAdminBlock && strpos($line, '});') !== false) {
        $inAdminBlock = false;
    }
    
    $newLines[] = $line;
}

file_put_contents($file, implode("\n", $newLines));
echo "Rotas corrigidas.\n";
