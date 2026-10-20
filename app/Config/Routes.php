<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers\Publica');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// Home
$routes->get('/', 'Home::index');
$routes->get('/home', 'Home::index');
$routes->get('/Home', 'Home::index');

// Gov.BR
$routes->get('/loginGovBr', 'Cadastros::loginGovBr');
$routes->get('/Home/acessarGovBr', 'Home::acessarGovBr');

// Mock Login (desenvolvimento)
$routes->get('mock_login', 'MockLogin::index');

// Cadastros
$routes->get('/Cadastros', 'Cadastros::index');
$routes->get('/Cadastros/dadosCandidato', 'Cadastros::dadosCandidato', ['as' => 'dadosCandidato']);
$routes->get('/Cadastros/dadosClassificatorios/(:num)/(:num)/(:num)', 'Cadastros::dadosClassificatorios/$1/$2/$3', ['as' => 'dadosClassificatorios']);
$routes->get('/Cadastros/sucessoClassificatorio/(:num)/(:num)/(:num)', 'Cadastros::sucessoClassificatorio/$1/$2/$3', ['as' => 'sucessoClassificatorio']);
$routes->get('/Cadastros/gerarComprovante/(:num)/(:num)/(:num)', 'Cadastros::gerarComprovante/$1/$2/$3', ['as' => 'gerarComprovante']);
$routes->get('/Cadastros/detalhesRecurso/(:any)', 'Cadastros::detalhesRecurso/$1', ['as' => 'detalhesRecurso']);
$routes->get('/Cadastros/logOut', 'Cadastros::logOut', ['as' => 'logOut']);

$routes->post('/Cadastros/registrarDadosPessoais', 'Cadastros::registrarDadosPessoais');
$routes->post('/Cadastros/registrarDadosClassificatorios', 'Cadastros::registrarDadosClassificatorios');

// Editais
$routes->get('/Editais', 'Editais::index');
$routes->get('/Editais/detalhes/(:num)', 'Editais::detalhes/$1', ['as' => 'detalhesEdital']);
$routes->get('/Editais/obterEdital/(:any)', 'Editais::obterEdital/$1');
$routes->get('/Editais/autenticarEdital/(:any)', 'Editais::autenticarEdital/$1');

// Perguntas Frequentes
$routes->get('/PerguntasFrequentes', 'PerguntasFrequentes::index');
$routes->get('/PerguntasFrequentes/buscarRespostas', 'PerguntasFrequentes::buscarRespostas');

// Transparência
$routes->get('/transparencia', 'Transparencia::index');
$routes->get('/transparencia/carregarTabela', 'Transparencia::carregarTabela');
$routes->get('/transparencia/carregarCargosPorEdital', 'Transparencia::carregarCargosPorEdital');

// API
$routes->get('api/cep', 'Api\CepController::buscar');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
