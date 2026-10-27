<?php
/**
 * Integra as rotas do admin em app/Config/Routes.php
 * Adiciona um $routes->group('adm', ...) com namespace App\Controllers\Admin
 */

$adminRoutesFile = 'adm/app/Config/Routes.php';
$publicRoutesFile = 'app/Config/Routes.php';

// Lê o arquivo de rotas do admin
$adminContent = file_get_contents($adminRoutesFile);

// Extrai apenas as linhas de definição de rotas (após o comentário "Route Definitions")
$lines = explode("\n", $adminContent);
$inRoutes = false;
$routeLines = [];

foreach ($lines as $line) {
    $trimmed = trim($line);
    
    // Ignora boilerplate inicial
    if (strpos($trimmed, '/*') === 0 || strpos($trimmed, ' *') === 0 || strpos($trimmed, '*/') === 0) {
        if (strpos($trimmed, 'Route Definitions') !== false) {
            $inRoutes = true;
        }
        continue;
    }
    
    if (strpos($trimmed, 'namespace ') === 0) continue;
    if (strpos($trimmed, '$routes = Services::routes()') === 0) continue;
    if (strpos($trimmed, '$routes->setDefaultNamespace') === 0) continue;
    if (strpos($trimmed, '$routes->setDefaultController') === 0) continue;
    if (strpos($trimmed, '$routes->setDefaultMethod') === 0) continue;
    if (strpos($trimmed, '$routes->setTranslateURIDashes') === 0) continue;
    if (strpos($trimmed, '$routes->set404Override') === 0) continue;
    if (strpos($trimmed, '$routes->setAutoRoute') === 0) continue;
    if (strpos($trimmed, '//') === 0 && !$inRoutes) continue;
    if ($trimmed === '') continue;
    
    // Para no final do arquivo (Additional Routing / require)
    if (strpos($trimmed, 'if (is_file') === 0) break;
    if (strpos($trimmed, 'require') === 0) break;
    
    // Ignora comentários do topo
    if (!$inRoutes && strpos($trimmed, '$routes->') !== 0) continue;
    
    if (strpos($trimmed, '$routes->') === 0) {
        $inRoutes = true;
    }
    
    if ($inRoutes) {
        $routeLines[] = $line;
    }
}

// Monta o bloco de rotas do admin
$adminBlock = "\n\n/*\n * --------------------------------------------------------------------\n";
$adminBlock .= " * Admin Routes (migrado de adm/app/Config/Routes.php)\n";
$adminBlock .= " * --------------------------------------------------------------------\n */\n";
$adminBlock .= "\$routes->group('adm', ['namespace' => 'App\\\\Controllers\\\\Admin'], function(\$routes) {\n";
foreach ($routeLines as $line) {
    $adminBlock .= "    " . $line . "\n";
}
$adminBlock .= "});\n";

// Lê o arquivo público
$publicContent = file_get_contents($publicRoutesFile);

// Insere o bloco antes do "Additional Routing"
$publicContent = str_replace(
    "/*\n * --------------------------------------------------------------------\n * Additional Routing\n",
    $adminBlock . "/*\n * --------------------------------------------------------------------\n * Additional Routing\n",
    $publicContent
);

file_put_contents($publicRoutesFile, $publicContent);

echo "Rotas do admin integradas em app/Config/Routes.php\n";
echo "Total de linhas de rotas do admin: " . count($routeLines) . "\n";
