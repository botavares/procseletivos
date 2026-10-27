<?php
/**
 * Atualiza chamadas service() nos controllers do admin para métodos renomeados
 */

$dir = 'app/Controllers/Admin';
$replacements = [
    "service('experienciaService'"     => "service('adminExperienciaService'",
    "service('escolaridadeService'"    => "service('adminEscolaridadeService'",
    "service('aperfeicoamentoService'" => "service('adminAperfeicoamentoService'",
    "service('criterioAdicionalService'" => "service('adminCriterioAdicionalService'",
    "service('desempateConfigService'" => "service('adminDesempateConfigService'",
    "service('candidatoRepository'"    => "service('adminCandidatoRepository'",
    "service('contratoRepository'"     => "service('adminContratoRepository'",
    "service('editalRepository'"       => "service('adminEditalRepository'",
];

$updated = 0;
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $original = $content;
        foreach ($replacements as $old => $new) {
            $content = str_replace($old, $new, $content);
        }
        if ($content !== $original) {
            file_put_contents($file->getPathname(), $content);
            $updated++;
        }
    }
}

echo "Controllers atualizados: $updated\n";
