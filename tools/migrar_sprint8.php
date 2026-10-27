<?php
/**
 * Script de Migração Sprint 8
 * Automatiza a cópia de arquivos de adm/app/ para app/ e atualiza namespaces
 */

$sourceBase = 'adm/app/';
$targetBase = 'app/';

$mappings = [
    // Origem relativa (adm/app/) => Destino relativo (app/)
    'Controllers/Admin'               => 'Controllers/Admin',   // mantém namespace
    'Models'                        => 'Models/Admin',
    'Services'                      => 'Services/Admin',
    'Repositories'                    => 'Repositories/Admin',
    'Enums'                         => 'Enums/Admin',
    'Exceptions'                    => 'Exceptions/Admin',
    'Traits'                        => 'Traits/Admin',
    'Commands'                      => 'Commands/Admin',
    'Helpers'                       => 'Helpers/Admin',
    'Views/Admin'                   => 'Views/Admin',
];

// Views na raiz do adm/app/Views/ (fora de Admin/)
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
        $targetPath = $dst . '/' . substr($item->getPathname(), strlen($src) + 1);
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

function updateNamespaces(string $file, array $replacements): void {
    $content = file_get_contents($file);
    foreach ($replacements as $old => $new) {
        // Atualiza namespace declaration
        $content = preg_replace(
            '/namespace\s+' . preg_quote($old, '/') . '(;|\\)/',
            'namespace ' . $new . '$1',
            $content
        );
        // Atualiza use statements
        $content = preg_replace(
            '/use\s+' . preg_quote($old, '/') . '(;|\\)/',
            'use ' . $new . '$1',
            $content
        );
        // Atualiza referências em type-hints e instanciações (App\Models\Xxx)
        $content = preg_replace(
            '/(\s|\\|\(|,)' . preg_quote($old, '/') . '\\/',
            '$1' . $new . '\\',
            $content
        );
    }
    file_put_contents($file, $content);
}

function updateViewPaths(string $file): void {
    $content = file_get_contents($file);
    // Atualiza referências como view('Admin/...') para garantir que funcionem
    // Não precisa mudar, pois app/Views/Admin/ já é o padrão
    file_put_contents($file, $content);
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

// Copiar views da raiz
$totalCopied += copyFiles($sourceBase . 'Views', $targetBase . 'Views/Admin', $rootViews);
echo "Copiado: Views raiz => Views/Admin (" . count($rootViews) . " arquivos)\n";

echo "Total copiado: $totalCopied arquivos\n\n";

// ============================================================
// Fase 2: Atualizar namespaces nos arquivos migrados
// ============================================================
echo "=== Fase 2: Atualizando namespaces ===\n";

$namespaceReplacements = [
    'App\\Models'            => 'App\\Models\\Admin',
    'App\\Services'          => 'App\\Services\\Admin',
    'App\\Repositories'      => 'App\\Repositories\\Admin',
    'App\\Enums'             => 'App\\Enums\\Admin',
    'App\\Exceptions'        => 'App\\Exceptions\\Admin',
    'App\\Traits'            => 'App\\Traits\\Admin',
    'App\\Commands'          => 'App\\Commands\\Admin',
];

// Atualizar todos os arquivos PHP migrados
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
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            updateNamespaces($file->getPathname(), $namespaceReplacements);
            $updatedCount++;
        }
    }
}

echo "Namespaces atualizados em $updatedCount arquivos\n\n";

// ============================================================
// Fase 3: Atualizar referências em controllers e services
// ============================================================
echo "=== Fase 3: Atualizando referências internas ===\n";

// Controllers e services precisam referenciar App\Models\Admin em vez de App\Models
$referenceDirs = [
    'app/Controllers/Admin',
    'app/Services/Admin',
    'app/Repositories/Admin',
    'app/Commands/Admin',
];

$refCount = 0;
foreach ($referenceDirs as $dir) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            $original = $content;
            foreach ($namespaceReplacements as $old => $new) {
                // Atualiza use statements que ainda não foram atualizados
                $content = preg_replace(
                    '/use\s+' . preg_quote($old, '/') . '(\\\w+)+;/',
                    'use ' . $new . '$1;',
                    $content
                );
                // Atualiza referências como new \App\Models\Xxx() ou type hints
                $content = preg_replace(
                    '/([^\\])' . preg_quote($old, '/') . '(\\\w+)/',
                    '$1' . $new . '$2',
                    $content
                );
            }
            if ($content !== $original) {
                file_put_contents($file->getPathname(), $content);
                $refCount++;
            }
        }
    }
}

echo "Referências atualizadas em $refCount arquivos\n\n";

echo "=== Migração concluída ===\n";
echo "Verifique os arquivos copiados e execute testes.\n";
