<?php
$file = 'app/Config/Routes.php';
$content = file_get_contents($file);

$lines = explode("\n", $content);
$inAdminBlock = false;
$newLines = [];

foreach ($lines as $line) {
    if (strpos($line, "\$routes->group('adm'") !== false) {
        $inAdminBlock = true;
    }
    
    if ($inAdminBlock) {
        // Remove a barra inicial do primeiro argumento da rota
        // Ex: $routes->get('/login', ... → $routes->get('login', ...
        $line = preg_replace("/(\$routes->(?:get|post|put|delete|patch|options|head|cli)\s*\(\s*['\"])\//", '\$1', $line);
    }
    
    if ($inAdminBlock && strpos($line, '});') !== false) {
        $inAdminBlock = false;
    }
    
    $newLines[] = $line;
}

file_put_contents($file, implode("\n", $newLines));
echo "Rotas corrigidas.\n";
