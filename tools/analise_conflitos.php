<?php
/**
 * Script para comparar arquivos entre app/ (raiz) e adm/app/
 * e gerar relatório de conflitos para Sprint 8
 */

$relatorio = [];

function compareFiles(string $path1, string $path2): array {
    if (!file_exists($path1) || !file_exists($path2)) {
        return ['status' => 'missing', 'diff_lines' => 0];
    }
    $content1 = file_get_contents($path1);
    $content2 = file_get_contents($path2);
    if ($content1 === $content2) {
        return ['status' => 'identical', 'diff_lines' => 0, 'size1' => strlen($content1), 'size2' => strlen($content2)];
    }
    $lines1 = explode("\n", $content1);
    $lines2 = explode("\n", $content2);
    $diff = count(array_diff_assoc($lines1, $lines2));
    return ['status' => 'different', 'diff_lines' => $diff, 'size1' => strlen($content1), 'size2' => strlen($content2)];
}

function findCommonFiles(string $dir1, string $dir2): array {
    $files1 = [];
    $files2 = [];
    $iter1 = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir1));
    $iter2 = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir2));
    foreach ($iter1 as $f) {
        if ($f->isFile() && $f->getExtension() === 'php') {
            $files1[$f->getBasename()] = $f->getPathname();
        }
    }
    foreach ($iter2 as $f) {
        if ($f->isFile() && $f->getExtension() === 'php') {
            $files2[$f->getBasename()] = $f->getPathname();
        }
    }
    $common = [];
    foreach ($files1 as $name => $path1) {
        if (isset($files2[$name])) {
            $common[$name] = [
                'path1' => $path1,
                'path2' => $files2[$name],
                'comparison' => compareFiles($path1, $files2[$name])
            ];
        }
    }
    return $common;
}

// Models
$relatorio['models'] = findCommonFiles('app/Models', 'adm/app/Models');

// Services (raiz apenas, sem subpastas ainda)
$relatorio['services_root'] = findCommonFiles('app/Services', 'adm/app/Services');

// BaseControllers
$relatorio['basecontroller'] = compareFiles('app/Controllers/BaseController.php', 'adm/app/Controllers/BaseController.php');

// Helpers
$relatorio['helpers'] = findCommonFiles('app/Helpers', 'adm/app/Helpers');

// Services.php config
$relatorio['config_services'] = compareFiles('app/Config/Services.php', 'adm/app/Config/Services.php');

// Routes.php config
$relatorio['config_routes'] = compareFiles('app/Config/Routes.php', 'adm/app/Config/Routes.php');

// Output
$output = "# Relatório de Conflitos — Sprint 8 (Unificação de Estrutura)\n\n";
$output .= "Gerado em: " . date('Y-m-d H:i:s') . "\n\n";

// BaseController
$output .= "## BaseController\n\n";
$bc = $relatorio['basecontroller'];
$output .= "- Status: **{$bc['status']}**\n";
if ($bc['status'] === 'different') {
    $output .= "- Tamanho app: {$bc['size1']} bytes | adm: {$bc['size2']} bytes\n";
    $output .= "- Linhas divergentes: {$bc['diff_lines']}\n";
}
$output .= "\n";

// Models
$output .= "## Models Comuns (app/Models vs adm/app/Models)\n\n";
$output .= "| Arquivo | Status | Tamanho App | Tamanho Adm | Linhas Dif |\n";
$output .= "|---------|--------|-------------|-------------|------------|\n";
foreach ($relatorio['models'] as $name => $data) {
    $c = $data['comparison'];
    $size1 = $c['size1'] ?? '-';
    $size2 = $c['size2'] ?? '-';
    $diff = $c['diff_lines'] ?? '-';
    $output .= "| $name | {$c['status']} | $size1 | $size2 | $diff |\n";
}
$output .= "\n";

// Services root
$output .= "## Services Comuns (raiz app/Services vs adm/app/Services)\n\n";
$output .= "| Arquivo | Status | Tamanho App | Tamanho Adm | Linhas Dif |\n";
$output .= "|---------|--------|-------------|-------------|------------|\n";
foreach ($relatorio['services_root'] as $name => $data) {
    $c = $data['comparison'];
    $size1 = $c['size1'] ?? '-';
    $size2 = $c['size2'] ?? '-';
    $diff = $c['diff_lines'] ?? '-';
    $output .= "| $name | {$c['status']} | $size1 | $size2 | $diff |\n";
}
$output .= "\n";

// Helpers
$output .= "## Helpers Comuns\n\n";
foreach ($relatorio['helpers'] as $name => $data) {
    $c = $data['comparison'];
    $output .= "- **$name**: {$c['status']}\n";
}
if (empty($relatorio['helpers'])) {
    $output .= "Nenhum helper comum encontrado.\n";
}
$output .= "\n";

// Configs
$output .= "## Configs Comuns\n\n";
$output .= "| Arquivo | Status | Tamanho App | Tamanho Adm | Linhas Dif |\n";
$output .= "|---------|--------|-------------|-------------|------------|\n";
foreach (['config_services', 'config_routes'] as $cfg) {
    $c = $relatorio[$cfg];
    $name = str_replace('config_', '', $cfg);
    $size1 = $c['size1'] ?? '-';
    $size2 = $c['size2'] ?? '-';
    $diff = $c['diff_lines'] ?? '-';
    $output .= "| Config/$name.php | {$c['status']} | $size1 | $size2 | $diff |\n";
}
$output .= "\n";

// Summary
$identicalModels = count(array_filter($relatorio['models'], fn($d) => $d['comparison']['status'] === 'identical'));
$differentModels = count(array_filter($relatorio['models'], fn($d) => $d['comparison']['status'] === 'different'));
$identicalServices = count(array_filter($relatorio['services_root'], fn($d) => $d['comparison']['status'] === 'identical'));
$differentServices = count(array_filter($relatorio['services_root'], fn($d) => $d['comparison']['status'] === 'different'));

$output .= "## Resumo\n\n";
$output .= "- **Models comuns:** " . count($relatorio['models']) . " (idênticos: $identicalModels, diferentes: $differentModels)\n";
$output .= "- **Services comuns (raiz):** " . count($relatorio['services_root']) . " (idênticos: $identicalServices, diferentes: $differentServices)\n";
$output .= "- **BaseController:** {$relatorio['basecontroller']['status']}\n";
$output .= "- **Helpers comuns:** " . count($relatorio['helpers']) . "\n";

file_put_contents('SPRINT8_CONFLITOS.md', $output);
echo "Relatório gerado: SPRINT8_CONFLITOS.md\n";
