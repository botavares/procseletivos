<?php

// Correção para FastCGI no Windows: SCRIPT_NAME vem com caminho absoluto do disco
// Isso quebra a detecção de URI do CI4. Forçamos o valor correto.
if (isset($_SERVER['SCRIPT_NAME']) && (strpos($_SERVER['SCRIPT_NAME'], 'C:/') === 0 || strpos($_SERVER['SCRIPT_NAME'], 'c:/') === 0)) {
    $_SERVER['SCRIPT_NAME'] = '/servicos/procseletivos/public/index.php';
}

// Correção para FastCGi no Windows:
// A QUERY_STRING pode vir no formato "path&param1=val1&param2=val2".
// Separamos o path (usado para roteamento via REQUEST_URI) dos parâmetros
// (usados para popular $_GET via parse_str do CI4).
if (isset($_SERVER['QUERY_STRING']) && strpos($_SERVER['QUERY_STRING'], '/') === 0) {
    $qs = $_SERVER['QUERY_STRING'];
    $ampPos = strpos($qs, '&');
    if ($ampPos !== false) {
        $pathPart  = substr($qs, 0, $ampPos);    // ex: /adm/CargosDesempateConfig ou /Editais/filtrar
        $queryPart = substr($qs, $ampPos + 1);   // ex: situacao=Vigentes&secretaria=2

        // O PHP criou uma chave espúria em $_GET com o nome do path; removemos.
        unset($_GET[$pathPart]);

        // QUERY_STRING deve conter apenas os parâmetros reais para que o CI4
        // popule $_GET corretamente em system/HTTP/IncomingRequest.php.
        $_SERVER['QUERY_STRING'] = $queryPart;

        // Repopula $_GET imediatamente (o CI4 fará parse_str novamente depois).
        parse_str($queryPart, $paramsGet);
        $_GET = array_merge($_GET, $paramsGet);

        // Reconstrói REQUEST_URI com path correto + parâmetros reais para o roteamento.
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
