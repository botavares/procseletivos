<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->get('/home', 'Home::index');
$routes->get('/Home', 'Home::index');

$routes->get('/loginGovBr', 'Cadastros::loginGovBr');
$routes->get('/Home/acessarGovBr', 'Home::acessarGovBr');


$routes->get('/Cadastros', 'Cadastros::index');
$routes->get('/cadastros', 'Cadastros::index');

$routes->get('/Cadastros/dadosCandidato', 'Cadastros::dadosCandidato', ['as'=>'dadosCandidato']);
$routes->get('/Cadastros/registrarDadosPessosais', 'Cadastros::registrarDadosPessosais');
$routes->post('/Cadastros/registrarDadosPessosais', 'Cadastros::registrarDadosPessosais');
$routes->get('/Cadastros/sucessoPessoais/(:num)', 'Cadastros::sucessoPessoais/$1' , ['as'=>'sucessoPessoais']);
$routes->get('/Cadastros/Error/(:num)', 'Cadastros::Error/$1' , ['as'=>'Error']);
$routes->get('/Cadastros/verificar/([A-F0-9\-]{35})','Cadastros::verificar/$1',['as' => 'Cadastros.verificar']);

$routes->get('/Cadastros/dadosClassificatorios/(:num)/(:num)/(:num)', 'Cadastros::dadosClassificatorios/$1/$2/$3', ['as'=>'dadosClassificatorios']);
$routes->get('/Cadastros/registrarDadosClassificatorios', 'Cadastros::registrarDadosClassificatorios');
$routes->post('/Cadastros/registrarDadosClassificatorios', 'Cadastros::registrarDadosClassificatorios');
$routes->get('/Cadastros/sucessoClassificatorio/(:num)/(:num)/(:num)', 'Cadastros::sucessoClassificatorio/$1/$2/$3' , ['as'=>'sucessoClassificatorio']);

$routes->get('/Editais', 'Editais::index');
$routes->get('/Editais/obterEdital/(:any)', 'Editais::obterEdital/$1');


$routes->get('/Cadastros/buscarCadastros', 'Cadastros::buscarCadastros', ['as'=>'buscarCadastros']);

$routes->get('/Cadastros/Consultar', 'Cadastros::Consultar');

$routes->get('/Cadastros/consultar', 'Cadastros::Consultar');
$routes->get('/Cadastros/analisar/(:num)', 'Cadastros::Analisar/$1');
$routes->get('/Cadastros/buscarCadastros', 'Cadastros::buscarCadastros');
$routes->get('/Cadastros/acessarResposta/(:num)', 'Cadastros::acessarResposta/$1');
$routes->get('/Cadastros/imprimirResposta/(:num)', 'Cadastros::imprimirResposta/$1');
$routes->get('/Cadastros/formGerarContestacao/(:num)', 'Cadastros::formGerarContestacao/$1');
$routes->get('/Cadastros/atualizarAndamento', 'Cadastros::atualizarAndamento');
$routes->get('/Cadastros/vincularCandidatoEdital/(:num)/(:num)', 'Cadastros::vincularCandidatoEdital/$1/$2');


$routes->get('/Cadastros/gerarComprovante/(:num)/(:num)/(:num)', 'Cadastros::gerarComprovante/$1/$2/$3', ['as'=>'gerarComprovante']);

$routes->get('/PerguntasFrequentes', 'PerguntasFrequentes::index');
$routes->get('/PerguntasFrequentes/buscarRespostas', 'PerguntasFrequentes::buscarRespostas');



$routes->post('/Cadastros/analisar', 'Cadastros::analisar');
$routes->post('/Cadastros/registrarContestacao', 'Cadastros::registrarContestacao');

$routes->get('/transparencia', 'Transparencia::index');

$routes->get('/transparencia/filtrarPorCargo', 'Transparencia::filtrarPorCargo');
$routes->get('/transparencia/carregarClassificacao', 'Transparencia::carregarClassificacao');

$routes->get('/Perguntas', 'Perguntas::index');

$routes->get('/Perguntas/buscarPerguntas', 'Perguntas::buscarPerguntas', ['as'=>'buscarPerguntas']);

$routes->get('/PdfGenerator', 'PdfGenerator::index');
$routes->get('/logOut', 'Cadastros::logOut', ['as'=>'logOut']);


$routes->get('api/cep', 'Api\CepController::buscar');

$routes->get('transparencia', 'Transparencia::index');
$routes->get('transparencia/carregarTabela', 'Transparencia::carregarTabela');


$routes->get('teste', 'teste::index');
/*






*/
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
