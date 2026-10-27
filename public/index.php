<?php

// LOG TEMPORÁRIO PARA DEBUG
$debugLog = __DIR__ . '/debug_request.log';
$debugData = sprintf(
    "[%s] URI: %s | QS: %s | SN: %s | PI: %s | SCRIPT_FILENAME: %s\n",
    date('Y-m-d H:i:s'),
    $_SERVER['REQUEST_URI'] ?? 'N/A',
    $_SERVER['QUERY_STRING'] ?? 'N/A',
    $_SERVER['SCRIPT_NAME'] ?? 'N/A',
    $_SERVER['PATH_INFO'] ?? 'N/A',
    $_SERVER['SCRIPT_FILENAME'] ?? 'N/A'
);
file_put_contents($debugLog, $debugData, FILE_APPEND | LOCK_EX);

// Correção para FastCGI no Windows: SCRIPT_NAME vem com caminho absoluto do disco
// Isso quebra a detecção de URI do CI4. Forçamos o valor correto.
if (isset($_SERVER['SCRIPT_NAME']) && (strpos($_SERVER['SCRIPT_NAME'], 'C:/') === 0 || strpos($_SERVER['SCRIPT_NAME'], 'c:/') === 0)) {
    $_SERVER['SCRIPT_NAME'] = '/servicos/procseletivos/public/index.php';
}

// Correção para FastCGI: quando a query string contém path + parâmetros
// (ex: /adm/CargosDesempateConfig&cargo=3&honeypot=), separa o path dos parâmetros reais
// para que o CI4 consiga rotear corretamente com uriProtocol = 'QUERY_STRING'
if (isset($_SERVER['QUERY_STRING']) && strpos($_SERVER['QUERY_STRING'], '/') === 0) {
    $qs = $_SERVER['QUERY_STRING'];
    $ampPos = strpos($qs, '&');
    if ($ampPos !== false) {
        $pathPart  = substr($qs, 0, $ampPos);    // ex: /adm/CargosDesempateConfig
        $queryPart = substr($qs, $ampPos + 1);  // ex: cargo=3&honeypot=

        // Mantém apenas o PATH no QUERY_STRING para o CI4 rotear corretamente
        // O PHP já parseou os parâmetros reais em $_GET, mas incluiu o path como chave espúria
        $_SERVER['QUERY_STRING'] = $pathPart;
        unset($_GET[$pathPart]);

        // Repopula $_GET para garantir que o CI4 consiga ler os parâmetros via getGet()
        parse_str($queryPart, $paramsGet);
        $_GET = array_merge($_GET, $paramsGet);

        // Reconstrói REQUEST_URI para refletir o path correto + parâmetros reais
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $qPos = strpos($requestUri, '?');
        if ($qPos !== false) {
            $requestUri = substr($requestUri, 0, $qPos);
        }
        $_SERVER['REQUEST_URI'] = $requestUri . '?' . $queryPart;
    }
}

// Check PHP version.
$minPhpVersion = '8.3'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION
    );

    exit($message);
}

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
chdir(FCPATH);

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// Load our paths config file
// This is the line that might need to be changed, depending on your folder structure.
require FCPATH . '../app/Config/Paths.php';
// ^^^ Change this line if you move your application folder

$paths = new Config\Paths();

// Location of the framework bootstrap file.
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

// Load environment settings from .env files into $_SERVER and $_ENV
require_once SYSTEMPATH . 'Config/DotEnv.php';
(new CodeIgniter\Config\DotEnv(ROOTPATH))->load();

/*
 * ---------------------------------------------------------------
 * GRAB OUR CODEIGNITER INSTANCE
 * ---------------------------------------------------------------
 *
 * The CodeIgniter class contains the core functionality to make
 * the application run, and does all of the dirty work to get
 * the pieces all working together.
 */

$app = Config\Services::codeigniter();
$app->initialize();
$context = is_cli() ? 'php-cli' : 'web';
$app->setContext($context);

/*
 *---------------------------------------------------------------
 * LAUNCH THE APPLICATION
 *---------------------------------------------------------------
 * Now that everything is setup, it's time to actually fire
 * up the engines and make this app do its thang.
 */

$app->run();
