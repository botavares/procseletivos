<?php
/**
 * Integra Services.php do admin em app/Config/Services.php da raiz
 * Renomeia métodos que colidem com prefixo 'admin'
 */

$adminFile = 'adm/app/Config/Services.php';
$publicFile = 'app/Config/Services.php';

// Métodos que colidem (precisam ser renomeados no admin)
$collisions = [
    'experienciaService'     => 'adminExperienciaService',
    'escolaridadeService'    => 'adminEscolaridadeService',
    'aperfeicoamentoService' => 'adminAperfeicoamentoService',
    'criterioAdicionalService' => 'adminCriterioAdicionalService',
    'desempateConfigService' => 'adminDesempateConfigService',
    'candidatoRepository'    => 'adminCandidatoRepository',
    'contratoRepository'     => 'adminContratoRepository',
    'editalRepository'       => 'adminEditalRepository',
];

// Lê o conteúdo do admin
$adminContent = file_get_contents($adminFile);

// Atualiza namespaces
$adminContent = str_replace('\\App\\Models\\', '\\App\\Models\\Admin\\', $adminContent);
$adminContent = str_replace('\\App\\Services\\', '\\App\\Services\\Admin\\', $adminContent);
$adminContent = str_replace('\\App\\Repositories\\', '\\App\\Repositories\\Admin\\', $adminContent);
$adminContent = str_replace('\\App\\Enums\\', '\\App\\Enums\\Admin\\', $adminContent);
$adminContent = str_replace('\\App\\Exceptions\\', '\\App\\Exceptions\\Admin\\', $adminContent);
$adminContent = str_replace('\\App\\Traits\\', '\\App\\Traits\\Admin\\', $adminContent);
$adminContent = str_replace('Admin\\Admin\\', 'Admin\\', $adminContent);

// Renomeia métodos que colidem
foreach ($collisions as $old => $new) {
    $adminContent = preg_replace('/public static function ' . preg_quote($old, '/') . '\b/', 'public static function ' . $new, $adminContent);
    // Atualiza também getSharedInstance correspondente
    $adminContent = str_replace("getSharedInstance('$old')", "getSharedInstance('$new')", $adminContent);
}

// Extrai o corpo da classe (entre as primeiras chaves da classe e a última chave)
$start = strpos($adminContent, 'class Services extends BaseService');
$bodyStart = strpos($adminContent, '{', $start) + 1;
$bodyEnd = strrpos($adminContent, '}');
$adminBody = trim(substr($adminContent, $bodyStart, $bodyEnd - $bodyStart));

// Lê o arquivo público
$publicContent = file_get_contents($publicFile);

// Insere o corpo do admin antes da última chave de fechamento
$lastBrace = strrpos($publicContent, '}');
$publicContent = substr_replace($publicContent, "\n\n    /* ================================================================\n     * SERVICES DO ADMIN (migrados de adm/app/Config/Services.php)\n     * ================================================================ */\n\n" . $adminBody . "\n", $lastBrace, 0);

file_put_contents($publicFile, $publicContent);

echo "Services.php integrado com sucesso.\n";
echo "Métodos renomeados: " . count($collisions) . "\n";
