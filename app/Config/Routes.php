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

// Gov.BR
$routes->get('/loginGovBr', 'Cadastros::loginGovBr');
$routes->get('/Home/acessarGovBr', 'Home::acessarGovBr');

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
$routes->get('/ProcessosSeletivos', 'ProcessosSeletivos::index');
$routes->get('/ProcessosSeletivos/detalhes/(:num)', 'ProcessosSeletivos::detalhes/$1', ['as' => 'detalhesEdital']);
$routes->get('/ProcessosSeletivos/obterEdital/(:any)/(:any)', 'ProcessosSeletivos::obterEdital/$1/$2', ['as' => 'obterEdital']);
$routes->get('/ProcessosSeletivos/autenticarEdital/(:any)', 'ProcessosSeletivos::autenticarEdital/$1');

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
 * Admin Routes (migrado de adm/app/Config/Routes.php)
 * --------------------------------------------------------------------
 */
$routes->group('adm', ['namespace' => 'App\Controllers\Admin'], function($routes) {
    $routes->get('', 'Home::index', ['as' => 'home']);
    $routes->get('/', 'Home::index');
    $routes->get('tempcheck', 'TempCheck::index');
    $routes->get('login', 'Login::index', ['as'=>'login']);
    $routes->post('login', 'Login::acesso', ['as'=>'login.acesso']);
    $routes->get('Login/formAlterarSenha', 'Login::formAlterarSenha', ['as'=>'Login.formAlterarSenha']);
    $routes->post('Login/alterarSenha', 'Login::alterarSenha', ['as'=>'Login.alterarSenha']);
    $routes->get('Login/logOut', 'Login::logOut', ['as'=>'Login.logOut']);
    $routes->get('Dashboard', 'Dashboard::index', ['as'=>'Dashboard']);
    $routes->get('seguranca/csrf', 'Seguranca::getCsrf');
    $routes->get('Classificacoes/(:num)/(:num)', 'Classificacoes::index/$1/$2', ['as'=>'Classificacoes']);
    $routes->get('Classificacoes/gerarClassificacao/(:num)/(:num)/(:num)', 'Classificacoes::gerarClassificacao/$1/$2/$3', ['as'=>'Classificacoes.gerarClassificacao']);
    $routes->get('Classificacoes/reprocessar/(:num)/(:num)', 'Classificacoes::reprocessar/$1/$2', ['as'=>'Classificacoes.reprocessar']);
    $routes->get('Classificacoes/gerarPdf/(:num)/(:num)', 'Classificacoes::gerarPdf/$1/$2', ['as'=>'Classificacoes.gerarPdf']);
    $routes->get('Classificacoes/exportarXlsx/(:num)/(:num)', 'Classificacoes::exportarXlsx/$1/$2', ['as'=>'Classificacoes.exportarXlsx']);
    $routes->post('Classificacoes/exportarTabela/(:num)/(:num)', 'Classificacoes::exportarTabela/$1/$2', ['as'=>'Classificacoes.exportarTabela']);
    $routes->get('Editais/editaisAtivos', 'Editais::editaisAtivos');
    $routes->get('Editais/editaisTodos', 'Editais::editaisTodos');
    $routes->post('Classificacoes/salvarEscolha', 'Classificacoes::salvarEscolha');
    $routes->get('Recursos/(:num)/(:num)/(:num)', 'Recursos::index/$1/$2/$3', ['as'=>'Recursos']);
    $routes->post('Recursos/cargosCandidato/', 'Recursos::cargosCandidato', ['as'=>'Recursos.cargosCandidato']);
    $routes->post('Recursos/registrar', 'Recursos::registrar', ['as'=>'Recursos.registrar']);
    $routes->get('Recursos/historico', 'Recursos::historico', ['as'=>'Recursos.historico']);
    $routes->post('Recursos/historico', 'Recursos::historico', ['as'=>'Recursos.historico.post']);
    $routes->get('Recursos/detalhes/(:num)', 'Recursos::detalhes/$1', ['as'=>'Recursos.detalhes']);
    $routes->get('Recursos/listar/(:num)/(:num)', 'Recursos::listar/$1/$2', ['as'=>'Recursos.listar']);
    $routes->post('Recursos/salvarEscolha', 'Recursos::salvarEscolha');
    $routes->get('Cargos', 'Cargos::index', ['as'=>'Cargos']);
    $routes->get('Cargos/formularioCadastro', 'Cargos::formularioCadastro', ['as'=>'Cargos.formularioCadastro']);
    $routes->post('Cargos/registrar', 'Cargos::registrar');
    $routes->get('Cargos/formularioAlteracao/(:num)', 'Cargos::formularioAlteracao/$1', ['as'=>'Cargos.formularioAlteracao']);
    $routes->post('Cargos/deletar', 'Cargos::deletar');
    $routes->get('Experiencias', 'Experiencias::index', ['as'=>'Experiencias']);
    $routes->get('Experiencias/formularioCadastro', 'Experiencias::formularioCadastro', ['as'=>'Experiencias.formularioCadastro']);
    $routes->post('Experiencias/registrar', 'Experiencias::registrar');
    $routes->get('Experiencias/formularioAlteracao/(:num)', 'Experiencias::formularioAlteracao/$1', ['as'=>'Experiencias.formularioAlteracao']);
    $routes->post('Experiencias/deletar', 'Experiencias::deletar');
    $routes->get('Escolaridades', 'Escolaridades::index', ['as'=>'Escolaridades']);
    $routes->get('Escolaridades/formularioCadastro', 'Escolaridades::formularioCadastro', ['as'=>'Escolaridades.formularioCadastro']);
    $routes->post('Escolaridades/registrar', 'Escolaridades::registrar');
    $routes->get('Escolaridades/formularioAlteracao/(:num)', 'Escolaridades::formularioAlteracao/$1', ['as'=>'Escolaridades.formularioAlteracao']);
    $routes->post('Escolaridades/deletar', 'Escolaridades::deletar');
    $routes->get('Aperfeicoamentos', 'Aperfeicoamentos::index', ['as'=>'Aperfeicoamentos']);
    $routes->get('Aperfeicoamentos/formularioCadastro', 'Aperfeicoamentos::formularioCadastro', ['as'=>'Aperfeicoamentos.formularioCadastro']);
    $routes->post('Aperfeicoamentos/registrar', 'Aperfeicoamentos::registrar');
    $routes->get('Aperfeicoamentos/formularioAlteracao/(:num)', 'Aperfeicoamentos::formularioAlteracao/$1', ['as'=>'Aperfeicoamentos.formularioAlteracao']);
    $routes->post('Aperfeicoamentos/deletar', 'Aperfeicoamentos::deletar');
    $routes->get('CriteriosAdicionais', 'CriteriosAdicionais::index', ['as'=>'CriteriosAdicionais']);
    $routes->get('CriteriosAdicionais/formularioCadastro', 'CriteriosAdicionais::formularioCadastro', ['as'=>'CriteriosAdicionais.formularioCadastro']);
    $routes->post('CriteriosAdicionais/registrar', 'CriteriosAdicionais::registrar');
    $routes->get('CriteriosAdicionais/formularioAlteracao/(:num)', 'CriteriosAdicionais::formularioAlteracao/$1', ['as'=>'CriteriosAdicionais.formularioAlteracao']);
    $routes->post('CriteriosAdicionais/deletar', 'CriteriosAdicionais::deletar');
    $routes->get('CargosExperiencias/formularioCargosExperiencia/(:num)', 'CargosExperiencias::formularioCargosExperiencia/$1', ['as'=>'CargosExperiencias.formularioCargosExperiencia']);
    $routes->post('CargosExperiencias/registrarAssociacaoCargoExperiencia', 'CargosExperiencias::registrarAssociacaoCargoExperiencia');
    $routes->post('CargosExperiencias/deletarAssociacaoCargoExperiencia', 'CargosExperiencias::deletarAssociacaoCargoExperiencia');
    $routes->get('CargosExperiencias/buscarAssociacao/(:num)', 'CargosExperiencias::buscarAssociacao/$1');
    $routes->get('CargosEscolaridades/formularioCargosEscolaridade/(:num)', 'CargosEscolaridades::formularioCargosEscolaridade/$1', ['as'=>'CargosEscolaridades.formularioCargosEscolaridade']);
    $routes->post('CargosEscolaridades/registrarAssociacaoCargoEscolaridade', 'CargosEscolaridades::registrarAssociacaoCargoEscolaridade');
    $routes->post('CargosEscolaridades/deletarAssociacaoCargoEscolaridade', 'CargosEscolaridades::deletarAssociacaoCargoEscolaridade');
    $routes->get('CargosEscolaridades/buscarAssociacao/(:num)', 'CargosEscolaridades::buscarAssociacao/$1');
    $routes->get('CargosCursos/formularioCargosCurso/(:num)', 'CargosCursos::formularioCargosCurso/$1', ['as'=>'CargosCursos.formularioCargosCurso']);
    $routes->post('CargosCursos/registrarAssociacaoCargoCursos', 'CargosCursos::registrarAssociacaoCargoCursos');
    $routes->post('CargosCursos/deletarAssociacaoCargoCursos', 'CargosCursos::deletarAssociacaoCargoCursos');
    $routes->get('CargosCursos/buscarAssociacao/(:num)', 'CargosCursos::buscarAssociacao/$1');
    $routes->get('CargosCriterios/formularioCargosCriterio/(:num)', 'CargosCriterios::formularioCargosCriterio/$1', ['as'=>'CargosCriterios.formularioCargosCriterio']);
    $routes->post('CargosCriterios/registrarAssociacaoCargoCriterio', 'CargosCriterios::registrarAssociacaoCargoCriterio');
    $routes->post('CargosCriterios/deletarAssociacaoCargoCriterio', 'CargosCriterios::deletarAssociacaoCargoCriterio');
    $routes->get('CargosCriterios/buscarAssociacao/(:num)', 'CargosCriterios::buscarAssociacao/$1');
    $routes->get('Instituicoes', 'Instituicoes::index');
    $routes->get('Instituicoes/formularioCadastro', 'Instituicoes::formularioCadastro', ['as'=>'Instituicoes.formularioCadastro']);
    $routes->post('Instituicoes/registrar', 'Instituicoes::registrar');
    $routes->get('Instituicoes/formularioAlteracao/(:num)', 'Instituicoes::formularioAlteracao/$1', ['as'=>'Instituicoes.formularioAlteracao']);
    $routes->post('Instituicoes/deletar', 'Instituicoes::deletar');
    $routes->get('Editais', 'Editais::index', ['as'=>'Editais']);
    $routes->get('Editais/encerrados', 'Editais::encerrados');
    $routes->get('Editais/formularioCadastro', 'Editais::formularioCadastro', ['as'=>'Editais.formularioCadastro']);
    $routes->post('Editais/registrar', 'Editais::registrar');
    $routes->get('Editais/formularioAlteracao/(:num)', 'Editais::formularioAlteracao/$1', ['as'=>'Editais.formularioAlteracao']);
    $routes->post('Editais/deletar', 'Editais::deletar');
    $routes->post('Editais/getCargosByEdital', 'Editais::getCargosByEdital');
    $routes->get('Editais/obterEdital/(:any)', 'Editais::obterEdital/$1', ['as'=>'Editais.obterEdital']);
    $routes->post('Candidatos/salvarEscolha', 'Candidatos::salvarEscolha');
    //$routes->get('Candidatos/formularioConvocar/(:num)', 'Candidatos::formularioConvocar/$1', ['as'=>'Candidatos.formularioConvocar']);
    $routes->get('Candidatos/(:num)/(:num)', 'Candidatos::index/$1/$2', ['as'=>'Candidatos']);
    $routes->get('Candidatos/exportarXlsx/(:num)/(:num)', 'Candidatos::exportarXlsx/$1/$2', ['as'=>'Candidatos.exportarXlsx']);
    $routes->get('Candidatos/formularioCadastro', 'Candidatos::formularioCadastro', ['as'=>'Candidatos.formularioCadastro']);
    $routes->post('Candidatos/registrar', 'Candidatos::registrar');
    $routes->get('Candidatos/exibirDados/(:num)/(:num)/(:num)', 'Candidatos::exibirDados/$1/$2/$3', ['as'=>'Candidatos.exibirDados']);
    $routes->get('Candidatos/atualizarInteresse/(:num)/(:num)/(:num)', 'Candidatos::atualizarInteresse/$1/$2/$3', ['as'=>'Candidatos.atualizarInteresse']);
    $routes->get('Candidatos/formularioAlteracao/(:num)', 'Candidatos::formularioAlteracao/$1', ['as'=>'Candidatos.formularioAlteracao']);
    $routes->post('Candidatos/deletar', 'Candidatos::deletar');
    $routes->post('Candidatos/getCandidatosByCargoAndEdital', 'Candidatos::getCandidatosByCargoAndEdital');
    $routes->get('Candidatos/formSituacaoCandidato/(:num)/(:num)/(:num)', 'Candidatos::formSituacaoCandidato/$1/$2/$3', ['as'=>'Candidatos.formSituacaoCandidato']);
    $routes->post('Candidatos/registrarSituacao', 'Candidatos::registrarSituacao', ['as'=>'Candidatos.registrarSituacao']);
    $routes->get('Convocados/(:num)/(:num)', 'Convocados::index/$1/$2', ['as'=>'Convocados']);
    $routes->get('Convocados/formularioConvocar/(:num)', 'Convocados::formularioConvocar/$1', ['as'=>'Convocados.formularioConvocar']);
    $routes->get('Convocados/convocados/', 'Convocados::convocados/', ['as'=>'Convocados.convocados']);
    $routes->post('Convocados/convocarCandidato', 'Convocados::convocarCandidato', ['as'=>'Convocados.convocarCandidato']);
    $routes->post('Convocados/atualizarComparecimento', 'Convocados::atualizarComparecimento', ['as'=>'Convocados.atualizarComparecimento']);
    $routes->post('Convocados/convocarCandidato', 'Convocados::convocarCandidato', ['as'=>'Convocados.convocarCandidato']);
    $routes->get('Convocados/convocados/', 'Convocados::convocados/', ['as'=>'Convocados.convocados']);
    $routes->get('Vagas', 'Vagas::index', ['as'=>'Vagas']);
    $routes->get('Vagas/formularioCadastro', 'Vagas::formularioCadastro', ['as'=>'Vagas.formularioCadastro']);
    $routes->post('Vagas/registrar', 'Vagas::registrar');
    $routes->get('Vagas/formularioAlteracao/(:num)', 'Vagas::formularioAlteracao/$1', ['as'=>'Vagas.formularioAlteracao']);
    $routes->post('Vagas/deletar', 'Vagas::deletar');
    $routes->post('Vagas/getSetoresByCargo', 'Vagas::getSetoresByCargo');
    $routes->get('Contratos', 'Contratos::index');
    $routes->get('Contratos/formularioCadastro', 'Contratos::formularioCadastro', ['as'=>'Contratos.formularioCadastro']);
    $routes->get('Contratos/formContratar/(:num)', 'Contratos::formContratar/$1', ['as'=>'Contratos.formContratar']);
    $routes->post('Contratos/registrar', 'Contratos::registrar');
    $routes->post('Contratos/alterar', 'Contratos::alterar');
    $routes->get('Contratos/contratosExpirando', 'Contratos::contratosExpirando', ['as'=>'Contratos.contratosExpirando']);
    $routes->post('Contratos/comunicar', 'Contratos::comunicar', ['as'=>'Contratos.comunicar']);
    $routes->post('Contratos/alterarContrato', 'Contratos::alterarContrato');
    $routes->get('Contratos/formComunicar/(:num)/(:num)', 'Contratos::formComunicar/$1/$2', ['as'=>'Contratos.formComunicar']);
    $routes->get('Contratos/imprimirContrato/(:num)', 'Contratos::imprimirContrato/$1', ['as'=>'Contratos.imprimirContrato']);
    $routes->get('Contratos/imprimirAditivo/(:num)', 'Contratos::imprimirAditivo/$1', ['as'=>'Contratos.imprimirAditivo']);
    $routes->get('Contratos/imprimirRescisao/(:num)', 'Contratos::imprimirRescisao/$1', ['as'=>'Contratos.imprimirRescisao']);
    $routes->post('Contratos/contratar/', 'Contratos::contratar', ['as'=>'Contratos.contratar']);
    $routes->get('Contratos/formAlterarContrato/(:num)', 'Contratos::formAlterarContrato/$1', ['as'=>'Contratos.formAlterarContrato']);
    $routes->get('Contratos/verificarStatusCandidato/(:num)', 'Contratos::verificarStatusCandidato/$1', ['as'=>'Contratos.verificarStatusCandidato']);
    $routes->get('Contratos/formAditivoRescindir/(:num)', 'Contratos::formAditivoRescindir/$1', ['as'=>'Contratos.formAditivoRescindir']);
    $routes->post('Contratos/aditivarRescindir', 'Contratos::aditivarRescindir');
    $routes->post('Contratos/deletar', 'Contratos::deletar');
    $routes->get('Ferias', 'Ferias::index');
    $routes->get('Ferias/formularioCadastro/(:num)', 'Ferias::formularioCadastro/$1', ['as'=>'Ferias.formularioCadastro']);
    $routes->get('Ferias/formularioAlteracao/(:num)', 'Ferias::formularioAlteracao/$1', ['as'=>'Ferias.formularioAlteracao']);
    $routes->post('Ferias/registrar', 'Ferias::registrar');
    $routes->post('Ferias/deletar', 'Ferias::deletar');
    $routes->get('Secretarias', 'Secretarias::index', ['as'=>'Secretarias']);
    $routes->get('Secretarias/formularioCadastro', 'Secretarias::formularioCadastro', ['as'=>'Secretarias.formularioCadastro']);
    $routes->post('Secretarias/registrar', 'Secretarias::registrar');
    $routes->post('Secretarias/deletar', 'Secretarias::deletar');
    $routes->get('Secretarias/formularioAlteracao/(:num)', 'Secretarias::formularioAlteracao/$1', ['as'=>'Secretarias.formularioAlteracao']);
    $routes->get('Seguros', 'Seguros::index');
    $routes->get('Seguros/formularioCadastro', 'Seguros::formularioCadastro', ['as'=>'Seguros.formularioCadastro']);
    $routes->post('Seguros/registrar', 'Seguros::registrar');
    $routes->get('Seguros/formularioAlteracao/(:num)', 'Seguros::formularioAlteracao/$1', ['as'=>'Seguros.formularioAlteracao']);
    $routes->post('Seguros/deletar', 'Seguros::deletar');
    $routes->get('Setores', 'Setores::index');
    $routes->get('Setores/formularioCadastro', 'Setores::formularioCadastro', ['as'=>'Setores.formularioCadastro']);
    $routes->get('Setores/formularioAlteracao/(:num)', 'Setores::formularioAlteracao/$1', ['as'=>'Setores.formularioAlteracao']);
    $routes->post('Setores/registrar', 'Setores::registrar');
    $routes->post('Setores/deletar', 'Setores::deletar');
    $routes->get('Relatorios', 'Relatorios::index', ['as'=>'Relatorios']);
    $routes->get('Relatorios/formCandidatosPorCurso', 'Relatorios::formCandidatosPorCurso', ['as'=>'Relatorios.formCandidatosPorCurso']);
    $routes->get('Relatorios/relatorioCandidatosPorCurso/(:num)', 'Relatorios::relatorioCandidatosPorCurso/$1', ['as'=>'Relatorios.relatorioCandidatosPorCurso']);
    $routes->post('Relatorios/relatorioCandidatosPorCurso', 'Relatorios::relatorioCandidatosPorCurso', ['as'=>'Relatorios.relatorioCandidatosPorCurso.post']);
    $routes->get('Relatorios/formCandidatosPorAbrangencia', 'Relatorios::formCandidatosPorAbrangencia', ['as'=>'Relatorios.formCandidatosPorAbrangencia']);
    $routes->get('Relatorios/relatorioCandidatosPorAbrangencia/(:num)', 'Relatorios::relatorioCandidatosPorAbrangencia/$1', ['as'=>'Relatorios.relatorioCandidatosPorAbrangencia']);
    $routes->post('Relatorios/relatorioCandidatosPorAbrangencia', 'Relatorios::relatorioCandidatosPorAbrangencia', ['as'=>'Relatorios.relatorioCandidatosPorAbrangencia.post']);
    $routes->get('Respostas', 'Respostas::index');
    $routes->post('Respostas/registrarResposta', 'Respostas::registrarResposta');
    $routes->get('Respostas/gerarResposta/(:num)', 'Respostas::gerarResposta/$1', ['as'=>'Respostas.gerarResposta']);
    $routes->get('Respostas/alterarResposta/(:num)', 'Respostas::alterarResposta/$1', ['as'=>'alterarResposta']);
    $routes->get('Respostas/gerarRespostaContestacao/(:num)', 'Respostas::gerarRespostaContestacao/$1', ['as'=>'Respostas.gerarRespostaContestacao']);
    $routes->get('Respostas/marcarRespondido/(:num)', 'Respostas::marcarRespondido/$1', ['as'=>'Respostas.marcarRespondido']);
    $routes->get('Perguntas', 'Perguntas::index');
    $routes->post('Perguntas/registrar', 'Perguntas::registrar');
    $routes->get('Perguntas/formularioCadastro', 'Perguntas::formularioCadastro', ['as'=>'Perguntas.formularioCadastro']);
    $routes->get('Perguntas/formularioAlteracao/(:num)', 'Perguntas::formularioAlteracao/$1', ['as'=>'Perguntas.formularioAlteracao']);
    $routes->get('Perguntas/getPerguntas/', 'Perguntas::getPerguntas/', ['as'=>'Perguntas.getPerguntas']);
    $routes->post('Perguntas/deletar', 'Perguntas::deletar');
    $routes->get('Migrador/migrar', 'Migrador::migrar');
    $routes->get('Migrador/adequarContratos', 'Migrador::adequarContratos');

    /* ============================================================
     * Processos Seletivos
     * ============================================================ */
    $routes->get('ProcessosSeletivos', 'ProcessosSeletivos::index', ['as' => 'AdminProcessosSeletivos']);
    $routes->get('ProcessosSeletivos/formularioCadastro', 'ProcessosSeletivos::formularioCadastro', ['as' => 'ProcessosSeletivos.formularioCadastro']);
    $routes->get('ProcessosSeletivos/formularioAlteracao/(:num)', 'ProcessosSeletivos::formularioAlteracao/$1', ['as' => 'ProcessosSeletivos.formularioAlteracao']);
    $routes->post('ProcessosSeletivos/registrar', 'ProcessosSeletivos::registrar');
    $routes->post('ProcessosSeletivos/deletar', 'ProcessosSeletivos::deletar');
    $routes->get('ProcessosSeletivos/buscarPorAno/(:num)', 'ProcessosSeletivos::buscarPorAno/$1');
    $routes->get('ProcessosSeletivos/buscarPorSituacao/(:num)', 'ProcessosSeletivos::buscarPorSituacao/$1');
    $routes->get('ProcessosSeletivos/buscarPorFaixaDeTempo/(:any)/(:any)', 'ProcessosSeletivos::buscarPorFaixaDeTempo/$1/$2');
    /* ============================================================
     * Processos Documentos
     * ============================================================ */
    $routes->get('ProcessosDocumentos', 'ProcessosDocumentos::index', ['as' => 'ProcessosDocumentos']);
    $routes->get('ProcessosDocumentos/formularioCadastro', 'ProcessosDocumentos::formularioCadastro', ['as' => 'ProcessosDocumentos.formularioCadastro']);
    $routes->get('ProcessosDocumentos/formularioCadastro/(:num)', 'ProcessosDocumentos::formularioCadastro/$1', ['as' => 'ProcessosDocumentos.formularioCadastro.id']);
    $routes->get('ProcessosDocumentos/formularioCadastro/(:num)/returnTo/(:any)', 'ProcessosDocumentos::formularioCadastro/$1/$2', ['as' => 'ProcessosDocumentos.formularioCadastro.return']);
    $routes->get('ProcessosDocumentos/formularioAlteracao/(:num)', 'ProcessosDocumentos::formularioAlteracao/$1', ['as' => 'ProcessosDocumentos.formularioAlteracao']);
    $routes->post('ProcessosDocumentos/registrar', 'ProcessosDocumentos::registrar');
    $routes->post('ProcessosDocumentos/deletar', 'ProcessosDocumentos::deletar');
    $routes->get('ProcessosDocumentos/buscarPorTipo/(:num)', 'ProcessosDocumentos::buscarPorTipo/$1');
    $routes->get('ProcessosDocumentos/buscarPorNumeroProcesso/(:num)/(:num)', 'ProcessosDocumentos::buscarPorNumeroProcesso/$1/$2');
    $routes->get('ProcessosDocumentos/buscarPorNumeroDocumentoEAno/(:num)/(:num)', 'ProcessosDocumentos::buscarPorNumeroDocumentoEAno/$1/$2');
    $routes->get('ProcessosDocumentos/buscarPorFaixaDeTempo/(:any)/(:any)', 'ProcessosDocumentos::buscarPorFaixaDeTempo/$1/$2');
    /* ============================================================
     * Processos Cargos
     * ============================================================ */
    $routes->get('ProcessosCargos', 'ProcessosCargos::index', ['as' => 'ProcessosCargos']);
    $routes->post('ProcessosCargos/registrar', 'ProcessosCargos::registrar');
    $routes->post('ProcessosCargos/deletar', 'ProcessosCargos::deletar');
    $routes->get('ProcessosCargos/buscarPorProcesso/(:num)', 'ProcessosCargos::buscarPorProcesso/$1');
    /* ============================================================
     * Rotas de Configuração de Critérios de Desempate por Cargo
     * ============================================================ */
    $routes->get('CargosDesempateConfig', 'CargosDesempateConfig::index', ['as' => 'CargosDesempateConfig']);
    $routes->get('CargosDesempateConfig/(:num)', 'CargosDesempateConfig::index/$1', ['as' => 'CargosDesempateConfig.cargo']);
    $routes->get('CargosDesempateConfig/listar/(:num)', 'CargosDesempateConfig::listar/$1', ['as' => 'CargosDesempateConfig.listar']);
    $routes->post('CargosDesempateConfig/salvar', 'CargosDesempateConfig::salvar', ['as' => 'CargosDesempateConfig.salvar']);
    $routes->post('CargosDesempateConfig/reordenar', 'CargosDesempateConfig::reordenar', ['as' => 'CargosDesempateConfig.reordenar']);
    $routes->post('CargosDesempateConfig/excluir/(:num)', 'CargosDesempateConfig::excluir/$1', ['as' => 'CargosDesempateConfig.excluir']);
    $routes->get('CargosDesempateConfig/excluir/(:num)', 'CargosDesempateConfig::excluir/$1', ['as' => 'CargosDesempateConfig.excluirGet']);
    $routes->get('CargosDesempateConfig/mover/(:num)', 'CargosDesempateConfig::mover/$1', ['as' => 'CargosDesempateConfig.mover']);
    $routes->get('CargosDesempateConfig/referencias/(:num)', 'CargosDesempateConfig::referencias/$1', ['as' => 'CargosDesempateConfig.referencias']);
});
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}