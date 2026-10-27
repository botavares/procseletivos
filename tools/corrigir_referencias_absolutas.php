<?php
/**
 * Corrige referências absolutas de namespaces nos arquivos migrados
 * Ex: \App\Models\Xxx → \App\Models\Admin\Xxx
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

$replacements = [
    '\\App\\Models\\'        => '\\App\\Models\\Admin\\',
    '\\App\\Services\\'       => '\\App\\Services\\Admin\\',
    '\\App\\Repositories\\'   => '\\App\\Repositories\\Admin\\',
    '\\App\\Enums\\'          => '\\App\\Enums\\Admin\\',
    '\\App\\Exceptions\\'     => '\\App\\Exceptions\\Admin\\',
    '\\App\\Traits\\'          => '\\App\\Traits\\Admin\\',
    '\\App\\Commands\\'       => '\\App\\Commands\\Admin\\',
];

$updated = 0;
foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            $original = $content;
            
            foreach ($replacements as $old => $new) {
                $content = str_replace($old, $new, $content);
            }
            
            // Corrige duplicações
            $content = str_replace('Admin\\Admin\\', 'Admin\\', $content);
            
            if ($content !== $original) {
                file_put_contents($file->getPathname(), $content);
                $updated++;
            }
        }
    }
}

echo "Arquivos atualizados: $updated\n";
