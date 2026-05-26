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
$routes->get('/', 'Home::index', ['as' => 'home']);

$routes->get('/login', 'Login::index', ['as'=>'login']);
$routes->post('/login', 'Login::acesso', ['as'=>'login.acesso']);
$routes->get('/Login/formAlterarSenha', 'Login::formAlterarSenha', ['as'=>'Login.formAlterarSenha']);
$routes->post('/Login/alterarSenha', 'Login::alterarSenha', ['as'=>'Login.alterarSenha']);
$routes->get('/Login/logOut', 'Login::logOut', ['as'=>'Login.logOut']);


$routes->get('/Dashboard', 'Dashboard::index', ['as'=>'Dashboard']);
$routes->get('/seguranca/csrf', 'Seguranca::getCsrf');

$routes->get('/Classificacoes/(:num)/(:num)', 'Classificacoes::index/$1/$2', ['as'=>'Classificacoes']);

$routes->get('/Classificacoes/gerarClassificacao/(:num)/(:num)/(:num)', 'Classificacoes::gerarClassificacao/$1/$2/$3', ['as'=>'Classificacoes.gerarClassificacao']);
$routes->get('/Classificacoes/reprocessar/(:num)/(:num)', 'Classificacoes::reprocessar/$1/$2', ['as'=>'Classificacoes.reprocessar']);
$routes->get('/Classificacoes/exportarXlsx/(:num)/(:num)', 'Classificacoes::exportarXlsx/$1/$2', ['as'=>'Classificacoes.exportarXlsx']);
$routes->post('/Classificacoes/salvarEscolha', 'Classificacoes::salvarEscolha');

$routes->get('/Recursos/(:num)/(:num)/(:num)', 'Recursos::index/$1/$2/$3', ['as'=>'Recursos']);
$routes->post('/Recursos/cargosCandidato/', 'Recursos::cargosCandidato', ['as'=>'Recursos.cargosCandidato']);
$routes->post('/Recursos/registrar', 'Recursos::registrar', ['as'=>'Recursos.registrar']);

$routes->get('/Cargos', 'Cargos::index');
$routes->get('/Cargos/formularioCadastro', 'Cargos::formularioCadastro', ['as'=>'Cargos.formularioCadastro']);
$routes->post('/Cargos/registrar', 'Cargos::registrar');
$routes->get('/Cargos/formularioAlteracao/(:num)', 'Cargos::formularioAlteracao/$1', ['as'=>'Cargos.formularioAlteracao']);
$routes->post('/Cargos/deletar', 'Cargos::deletar');

$routes->get('/Instituicoes', 'Instituicoes::index');
$routes->get('/Instituicoes/formularioCadastro', 'Instituicoes::formularioCadastro', ['as'=>'Instituicoes.formularioCadastro']);
$routes->post('/Instituicoes/registrar', 'Instituicoes::registrar');
$routes->get('/Instituicoes/formularioAlteracao/(:num)', 'Instituicoes::formularioAlteracao/$1', ['as'=>'Instituicoes.formularioAlteracao']);
$routes->post('/Instituicoes/deletar', 'Instituicoes::deletar');

$routes->get('/Editais', 'Editais::index');
$routes->get('/Editais/encerrados', 'Editais::encerrados');
$routes->get('/Editais/formularioCadastro', 'Editais::formularioCadastro', ['as'=>'Editais.formularioCadastro']);
$routes->post('/Editais/registrar', 'Editais::registrar');
$routes->get('/Editais/formularioAlteracao/(:num)', 'Editais::formularioAlteracao/$1', ['as'=>'Editais.formularioAlteracao']);
$routes->post('/Editais/deletar', 'Editais::deletar');
$routes->post('/Editais/getCargosByEdital', 'Editais::getCargosByEdital');
$routes->get('/Editais/obterEdital/(:any)', 'Editais::obterEdital/$1', ['as'=>'Editais.obterEdital']);
$routes->post('/Candidatos/salvarEscolha', 'Candidatos::salvarEscolha');
//$routes->get('/Candidatos/formularioConvocar/(:num)', 'Candidatos::formularioConvocar/$1', ['as'=>'Candidatos.formularioConvocar']);

$routes->get('/Candidatos/(:num)/(:num)', 'Candidatos::index/$1/$2', ['as'=>'Candidatos']);
$routes->get('/Candidatos/formularioCadastro', 'Candidatos::formularioCadastro', ['as'=>'Candidatos.formularioCadastro']);
$routes->post('/Candidatos/registrar', 'Candidatos::registrar');
$routes->get('/Candidatos/exibirDados/(:num)/(:num)/(:num)', 'Candidatos::exibirDados/$1/$2/$3', ['as'=>'Candidatos.exibirDados']);
$routes->get('/Candidatos/atualizarInteresse/(:num)/(:num)/(:num)', 'Candidatos::atualizarInteresse/$1/$2/$3', ['as'=>'Candidatos.atualizarInteresse']);
$routes->get('/Candidatos/formularioAlteracao/(:num)', 'Candidatos::formularioAlteracao/$1', ['as'=>'Candidatos.formularioAlteracao']);
$routes->post('/Candidatos/deletar', 'Candidatos::deletar');
$routes->post('/Candidatos/getCandidatosByCargoAndEdital', 'Candidatos::getCandidatosByCargoAndEdital');
$routes->get('/Candidatos/formSituacaoCandidato/(:num)/(:num)/(:num)', 'Candidatos::formSituacaoCandidato/$1/$2/$3', ['as'=>'Candidatos.formSituacaoCandidato']);
$routes->post('/Candidatos/registrarSituacao', 'Candidatos::registrarSituacao', ['as'=>'Candidatos.registrarSituacao']);

$routes->get('/Convocados/(:num)/(:num)', 'Convocados::index/$1/$2', ['as'=>'Convocados']);
$routes->get('/Convocados/formularioConvocar/(:num)', 'Convocados::formularioConvocar/$1', ['as'=>'Convocados.formularioConvocar']);
$routes->get('/Convocados/convocados/', 'Convocados::convocados/', ['as'=>'Convocados.convocados']);
$routes->post('/Convocados/convocarCandidato', 'Convocados::convocarCandidato', ['as'=>'Convocados.convocarCandidato']);
$routes->post('/Convocados/atualizarComparecimento', 'Convocados::atualizarComparecimento', ['as'=>'Convocados.atualizarComparecimento']);
$routes->post('/Convocados/convocarCandidato', 'Convocados::convocarCandidato', ['as'=>'Convocados.convocarCandidato']);
$routes->get('/Convocados/convocados/', 'Convocados::convocados/', ['as'=>'Convocados.convocados']);

$routes->get('/Vagas', 'Vagas::index');
$routes->get('/Vagas/formularioCadastro', 'Vagas::formularioCadastro', ['as'=>'Vagas.formularioCadastro']);
$routes->post('/Vagas/registrar', 'Vagas::registrar');
$routes->get('/Vagas/formularioAlteracao/(:num)', 'Vagas::formularioAlteracao/$1', ['as'=>'Vagas.formularioAlteracao']);
$routes->post('/Vagas/deletar', 'Vagas::deletar');
$routes->post('/Vagas/getSetoresByCargo', 'Vagas::getSetoresByCargo');

$routes->get('/Contratos', 'Contratos::index');

$routes->get('/Contratos/formularioCadastro', 'Contratos::formularioCadastro', ['as'=>'Contratos.formularioCadastro']);
$routes->get('/Contratos/formContratar/(:num)', 'Contratos::formContratar/$1', ['as'=>'Contratos.formContratar']);
$routes->post('/Contratos/registrar', 'Contratos::registrar');
$routes->post('/Contratos/alterar', 'Contratos::alterar');

$routes->get('/Contratos/contratosExpirando', 'Contratos::contratosExpirando', ['as'=>'Contratos.contratosExpirando']);
$routes->post('/Contratos/comunicar', 'Contratos::comunicar', ['as'=>'Contratos.comunicar']);
$routes->post('/Contratos/alterarContrato', 'Contratos::alterarContrato');

$routes->get('/Contratos/formComunicar/(:num)/(:num)', 'Contratos::formComunicar/$1/$2', ['as'=>'Contratos.formComunicar']);
$routes->get('/Contratos/imprimirContrato/(:num)', 'Contratos::imprimirContrato/$1', ['as'=>'Contratos.imprimirContrato']);
$routes->get('/Contratos/imprimirAditivo/(:num)', 'Contratos::imprimirAditivo/$1', ['as'=>'Contratos.imprimirAditivo']);
$routes->get('/Contratos/imprimirRescisao/(:num)', 'Contratos::imprimirRescisao/$1', ['as'=>'Contratos.imprimirRescisao']);
$routes->post('/Contratos/contratar/', 'Contratos::contratar', ['as'=>'Contratos.contratar']);
$routes->get('/Contratos/formAlterarContrato/(:num)', 'Contratos::formAlterarContrato/$1', ['as'=>'Contratos.formAlterarContrato']);
$routes->get('/Contratos/verificarStatusCandidato/(:num)', 'Contratos::verificarStatusCandidato/$1', ['as'=>'Contratos.verificarStatusCandidato']);
$routes->get('/Contratos/formAditivoRescindir/(:num)', 'Contratos::formAditivoRescindir/$1', ['as'=>'Contratos.formAditivoRescindir']);
$routes->post('/Contratos/aditivarRescindir', 'Contratos::aditivarRescindir');
$routes->post('/Contratos/deletar', 'Contratos::deletar');

$routes->get('/Ferias', 'Ferias::index');
$routes->get('/Ferias/formularioCadastro/(:num)', 'Ferias::formularioCadastro/$1', ['as'=>'Ferias.formularioCadastro']);
$routes->get('/Ferias/formularioAlteracao/(:num)', 'Ferias::formularioAlteracao/$1', ['as'=>'Ferias.formularioAlteracao']);
$routes->post('/Ferias/registrar', 'Ferias::registrar');
$routes->post('/Ferias/deletar', 'Ferias::deletar');

$routes->get('/Secretarias', 'Secretarias::index');
$routes->get('/Secretarias/formularioCadastro', 'Secretarias::formularioCadastro', ['as'=>'Secretarias.formularioCadastro']);
$routes->post('/Secretarias/registrar', 'Secretarias::registrar');
$routes->post('/Secretarias/deletar', 'Secretarias::deletar');
$routes->get('/Secretarias/formularioAlteracao/(:num)', 'Secretarias::formularioAlteracao/$1', ['as'=>'Secretarias.formularioAlteracao']);

$routes->get('/Seguros', 'Seguros::index');
$routes->get('/Seguros/formularioCadastro', 'Seguros::formularioCadastro', ['as'=>'Seguros.formularioCadastro']);
$routes->post('/Seguros/registrar', 'Seguros::registrar');
$routes->get('/Seguros/formularioAlteracao/(:num)', 'Seguros::formularioAlteracao/$1', ['as'=>'Seguros.formularioAlteracao']);
$routes->post('/Seguros/deletar', 'Seguros::deletar');

$routes->get('/Setores', 'Setores::index');
$routes->get('/Setores/formularioCadastro', 'Setores::formularioCadastro', ['as'=>'Setores.formularioCadastro']);
$routes->get('/Setores/formularioAlteracao/(:num)', 'Setores::formularioAlteracao/$1', ['as'=>'Setores.formularioAlteracao']);
$routes->post('/Setores/registrar', 'Setores::registrar');
$routes->post('/Setores/deletar', 'Setores::deletar');

$routes->get('/Relatorios', 'Relatorios::index', ['as'=>'Relatorios']);
$routes->get('/Relatorios/formCandidatosPorCurso', 'Relatorios::formCandidatosPorCurso', ['as'=>'Relatorios.formCandidatosPorCurso']);
$routes->get('/Relatorios/relatorioCandidatosPorCurso/(:num)', 'Relatorios::relatorioCandidatosPorCurso/$1', ['as'=>'Relatorios.relatorioCandidatosPorCurso']);
$routes->post('/Relatorios/relatorioCandidatosPorCurso', 'Relatorios::relatorioCandidatosPorCurso', ['as'=>'Relatorios.relatorioCandidatosPorCurso.post']);

$routes->get('/Relatorios/formCandidatosPorAbrangencia', 'Relatorios::formCandidatosPorAbrangencia', ['as'=>'Relatorios.formCandidatosPorAbrangencia']);
$routes->get('/Relatorios/relatorioCandidatosPorAbrangencia/(:num)', 'Relatorios::relatorioCandidatosPorAbrangencia/$1', ['as'=>'Relatorios.relatorioCandidatosPorAbrangencia']);
$routes->post('/Relatorios/relatorioCandidatosPorAbrangencia', 'Relatorios::relatorioCandidatosPorAbrangencia', ['as'=>'Relatorios.relatorioCandidatosPorAbrangencia.post']);

$routes->get('/Respostas', 'Respostas::index');
$routes->post('/Respostas/registrarResposta', 'Respostas::registrarResposta');
$routes->get('/Respostas/gerarResposta/(:num)', 'Respostas::gerarResposta/$1', ['as'=>'Respostas.gerarResposta']);
$routes->get('/Respostas/alterarResposta/(:num)', 'Respostas::alterarResposta/$1', ['as'=>'alterarResposta']);
$routes->get('/Respostas/gerarRespostaContestacao/(:num)', 'Respostas::gerarRespostaContestacao/$1', ['as'=>'Respostas.gerarRespostaContestacao']);
$routes->get('/Respostas/marcarRespondido/(:num)', 'Respostas::marcarRespondido/$1', ['as'=>'Respostas.marcarRespondido']);

$routes->get('/Perguntas', 'Perguntas::index');
$routes->post('/Perguntas/registrar', 'Perguntas::registrar');
$routes->get('/Perguntas/formularioCadastro', 'Perguntas::formularioCadastro', ['as'=>'Perguntas.formularioCadastro']);
$routes->get('/Perguntas/formularioAlteracao/(:num)', 'Perguntas::formularioAlteracao/$1', ['as'=>'Perguntas.formularioAlteracao']);
$routes->get('/Perguntas/getPerguntas/', 'Perguntas::getPerguntas/', ['as'=>'Perguntas.getPerguntas']);
$routes->post('/Perguntas/deletar', 'Perguntas::deletar');

$routes->get('/Migrador/migrar', 'Migrador::migrar');
$routes->get('/Migrador/adequarContratos', 'Migrador::adequarContratos');




if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
