<?php
/**
 * Script de Migração Sprint 8 — Versão Final e Corrigida
 * Copia arquivos de adm/app/ para app/ e ajusta namespaces com regex seguro.
 */

$sourceBase = 'adm/app/';
$targetBase = 'app/';

$mappings = [
    'Controllers/Admin'    => 'Controllers/Admin',
    'Models'               => 'Models/Admin',
    'Services'             => 'Services/Admin',
    'Repositories'         => 'Repositories/Admin',
    'Enums'                => 'Enums/Admin',
    'Exceptions'           => 'Exceptions/Admin',
    'Traits'               => 'Traits/Admin',
    'Commands'             => 'Commands/Admin',
    'Helpers'              => 'Helpers/Admin',
    'Views/Admin'          => 'Views/Admin',
];

$rootViews = [
    'layoutDash.php',
    'layoutLimpo.php',
    'layoutLogin.php',
    'layoutSimples.php',
    'resume.php',
    'welcome_message.php',
];

function rrmdir(string $dir): void {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                $path = $dir . "/" . $object;
                if (is_dir($path)) {
                    rrmdir($path);
                } else {
                    unlink($path);
                }
            }
        }
        rmdir($dir);
    }
}

function ensureDir(string $dir): void {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

function copyDir(string $src, string $dst): int {
    ensureDir($dst);
    $count = 0;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($src, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $item) {
        $relative = substr($item->getPathname(), strlen($src) + 1);
        $targetPath = $dst . '/' . $relative;
        if ($item->isDir()) {
            ensureDir($targetPath);
        } else {
            copy($item->getPathname(), $targetPath);
            $count++;
        }
    }
    return $count;
}

function copyFiles(string $srcDir, string $dstDir, array $files): int {
    ensureDir($dstDir);
    $count = 0;
    foreach ($files as $file) {
        $src = $srcDir . '/' . $file;
        if (file_exists($src)) {
            copy($src, $dstDir . '/' . $file);
            $count++;
        }
    }
    return $count;
}

function fixNamespacesInFile(string $file): void {
    $content = file_get_contents($file);
    
    // Regex para namespace declarations: insere \Admin após Models/Services/etc.
    // Lookahead garante que paramos logo após o nome da camada (antes de \ ou ;)
    $content = preg_replace(
        '/namespace\s+App\\\\(Models|Services|Repositories|Enums|Exceptions|Traits|Commands)(?=\\\\|;)/',
        'namespace App\\\\$1\\\\Admin',
        $content
    );
    
    // Regex para use statements
    $content = preg_replace(
        '/use\s+App\\\\(Models|Services|Repositories|Enums|Exceptions|Traits|Commands)(?=\\\\|;)/',
        'use App\\\\$1\\\\Admin',
        $content
    );
    
    // Corrige duplicações acidentais (se houver)
    $content = str_replace('Admin\\\\Admin\\\\', 'Admin\\\\', $content);
    $content = str_replace('Admin\\\\Admin;', 'Admin;', $content);
    
    file_put_contents($file, $content);
}

// ============================================================
// Fase 1: Limpar diretórios de destino
// ============================================================
echo "=== Fase 1: Limpando diretórios de destino ===\n";
foreach ($mappings as $src => $dst) {
    $dstPath = $targetBase . $dst;
    if (is_dir($dstPath)) {
        rrmdir($dstPath);
        echo "Removido: $dstPath\n";
    }
}
$viewsAdmin = $targetBase . 'Views/Admin';
if (is_dir($viewsAdmin)) {
    rrmdir($viewsAdmin);
    echo "Removido: $viewsAdmin\n";
}

// ============================================================
// Fase 2: Copiar arquivos
// ============================================================
echo "\n=== Fase 2: Copiando arquivos ===\n";

$totalCopied = 0;
foreach ($mappings as $src => $dst) {
    $srcPath = $sourceBase . $src;
    $dstPath = $targetBase . $dst;
    if (is_dir($srcPath)) {
        $count = copyDir($srcPath, $dstPath);
        echo "Copiado: $src => $dst ($count arquivos)\n";
        $totalCopied += $count;
    } else {
        echo "AVISO: Diretório não encontrado: $srcPath\n";
    }
}

$totalCopied += copyFiles($sourceBase . 'Views', $targetBase . 'Views/Admin', $rootViews);
echo "Copiado: Views raiz => Views/Admin (" . count($rootViews) . " arquivos)\n";

echo "Total copiado: $totalCopied arquivos\n\n";

// ============================================================
// Fase 3: Atualizar namespaces
// ============================================================
echo "=== Fase 3: Atualizando namespaces ===\n";

$dirsToUpdate = [
    'app/Controllers/Admin',
    'app/Models/Admin',
    'app/Services/Admin',
    'app/Repositories/Admin',
    'app/Enums/Admin',
    'app/Exceptions/Admin',
    'app/Traits/Admin',
    'app/Commands/Admin',
    'app/Helpers/Admin',
];

$updatedCount = 0;
foreach ($dirsToUpdate as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            fixNamespacesInFile($file->getPathname());
            $updatedCount++;
        }
    }
}

echo "Namespaces atualizados em $updatedCount arquivos\n\n";

// ============================================================
// Fase 4: Verificar integridade
// ============================================================
echo "=== Fase 4: Verificando integridade ===\n";
$emptyFiles = 0;
$duplicatedAdmin = 0;
foreach ($dirsToUpdate as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            if ($file->getSize() === 0) {
                echo "ARQUIVO VAZIO: " . $file->getPathname() . "\n";
                $emptyFiles++;
            }
            $content = file_get_contents($file->getPathname());
            if (strpos($content, 'Admin\\\\Admin') !== false || strpos($content, 'Admin\\\\Admin') !== false) {
                echo "DUPLICADO: " . $file->getPathname() . "\n";
                $duplicatedAdmin++;
            }
        }
    }
}

if ($emptyFiles === 0 && $duplicatedAdmin === 0) {
    echo "Integridade OK — nenhum arquivo vazio ou duplicado.\n";
} else {
    echo "ATENÇÃO: $emptyFiles arquivos vazios, $duplicatedAdmin com Admin duplicado.\n";
}

echo "\n=== Migração concluída ===\n";
