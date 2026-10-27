<?php
/**
 * Corrige namespaces nos arquivos migrados usando str_replace simples
 */

$dirs = [
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

// Mapeamentos de substituição para namespace declarations
$nsReplacements = [
    "namespace App\Models;"          => "namespace App\Models\Admin;",
    "namespace App\Services;"        => "namespace App\Services\Admin;",
    "namespace App\Repositories;"    => "namespace App\Repositories\Admin;",
    "namespace App\Enums;"           => "namespace App\Enums\Admin;",
    "namespace App\Exceptions;"      => "namespace App\Exceptions\Admin;",
    "namespace App\Traits;"           => "namespace App\Traits\Admin;",
    "namespace App\Commands;"         => "namespace App\Commands\Admin;",
];

// Sub-namespaces (ex: namespace App\Services\Candidatos;)
$subNsPatterns = [
    "namespace App\Models\\"        => "namespace App\Models\Admin\\",
    "namespace App\Services\\"       => "namespace App\Services\Admin\\",
    "namespace App\Repositories\\"   => "namespace App\Repositories\Admin\\",
    "namespace App\Enums\\"          => "namespace App\Enums\Admin\\",
    "namespace App\Exceptions\\"     => "namespace App\Exceptions\Admin\\",
    "namespace App\Traits\\"          => "namespace App\Traits\Admin\\",
    "namespace App\Commands\\"       => "namespace App\Commands\Admin\\",
];

// Use statements
$useReplacements = [
    "use App\Models\\"               => "use App\Models\Admin\\",
    "use App\Services\\"             => "use App\Services\Admin\\",
    "use App\Repositories\\"         => "use App\Repositories\Admin\\",
    "use App\Enums\\"                 => "use App\Enums\Admin\\",
    "use App\Exceptions\\"           => "use App\Exceptions\Admin\\",
    "use App\Traits\\"                => "use App\Traits\Admin\\",
    "use App\Commands\\"              => "use App\Commands\Admin\\",
];

$updated = 0;
foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            $original = $content;
            
            // Aplica replacements de namespace
            foreach ($nsReplacements as $old => $new) {
                $content = str_replace($old, $new, $content);
            }
            
            // Aplica replacements de sub-namespace
            foreach ($subNsPatterns as $old => $new) {
                $content = str_replace($old, $new, $content);
            }
            
            // Aplica replacements de use statements
            foreach ($useReplacements as $old => $new) {
                $content = str_replace($old, $new, $content);
            }
            
            // Evita duplicar Admin\Admin se já foi processado
            $content = str_replace("Admin\\Admin\\", "Admin\\", $content);
            
            if ($content !== $original) {
                file_put_contents($file->getPathname(), $content);
                $updated++;
            }
        }
    }
}

echo "Arquivos atualizados: $updated\n";
