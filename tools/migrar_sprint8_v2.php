<?php
/**
 * Script de Migração Sprint 8 - Versão 2
 * Abordagem robusta: str_replace em vez de regex problemáticos
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

function updateFile(string $file): void {
    $content = file_get_contents($file);
    $lines = explode("\n", $content);
    $newLines = [];
    
    foreach ($lines as $line) {
        $trimmed = ltrim($line);
        
        // Atualiza namespace declarations
        if (preg_match('/^namespace\s+App\\(Models|Services|Repositories|Enums|Exceptions|Traits|Commands)\b/', $trimmed)) {
            $line = preg_replace('/^(namespace\s+App\\)(Models|Services|Repositories|Enums|Exceptions|Traits|Commands)\b/', '$1$2\\Admin', $line);
        }
        
        // Atualiza use statements
        if (preg_match('/^use\s+App\\(Models|Services|Repositories|Enums|Exceptions|Traits|Commands)\\/', $trimmed)) {
            // Evita duplicar Admin\Admin
            if (!preg_match('/^use\s+App\\(Models|Services|Repositories|Enums|Exceptions|Traits|Commands)\\Admin\\/', $trimmed)) {
                $line = preg_replace('/^(use\s+App\\)(Models|Services|Repositories|Enums|Exceptions|Traits|Commands)\\/', '$1$2\\Admin\\', $line);
            }
        }
        
        $newLines[] = $line;
    }
    
    file_put_contents($file, implode("\n", $newLines));
}

// ============================================================
// Fase 1: Copiar arquivos
// ============================================================
echo "=== Fase 1: Copiando arquivos ===\n";

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
// Fase 2: Atualizar namespaces
// ============================================================
echo "=== Fase 2: Atualizando namespaces ===\n";

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
            updateFile($file->getPathname());
            $updatedCount++;
        }
    }
}

echo "Namespaces atualizados em $updatedCount arquivos\n\n";

// ============================================================
// Fase 3: Verificar integridade
// ============================================================
echo "=== Fase 3: Verificando integridade ===\n";
$emptyFiles = 0;
foreach ($dirsToUpdate as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getSize() === 0) {
            echo "ARQUIVO VAZIO: " . $file->getPathname() . "\n";
            $emptyFiles++;
        }
    }
}

if ($emptyFiles === 0) {
    echo "Nenhum arquivo vazio encontrado. Migração OK!\n";
} else {
    echo "ATENÇÃO: $emptyFiles arquivos vazios encontrados!\n";
}

echo "\n=== Migração concluída ===\n";
