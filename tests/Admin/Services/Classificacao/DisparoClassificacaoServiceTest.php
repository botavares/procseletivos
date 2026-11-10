<?php

declare(strict_types=1);

namespace Tests\Admin\Services\Classificacao;

use App\Models\Admin\LogsModel;
use App\Models\Admin\UsuarioModel;
use App\Models\ProcessosSeletivosModel;
use App\Models\SituacaoProcessosModel;
use App\Services\Admin\Classificacao\ClassificacaoService;
use App\Services\Admin\Classificacao\DisparoClassificacaoService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class DisparoClassificacaoServiceTest extends CIUnitTestCase
{
    private DisparoClassificacaoService $service;
    private $usuarioModelMock;
    private $situacaoProcessosModelMock;
    private $processosModelMock;
    private $logsModelMock;
    private $classificacaoServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usuarioModelMock            = $this->createMock(UsuarioModel::class);
        $this->situacaoProcessosModelMock    = $this->createMock(SituacaoProcessosModel::class);
        $this->processosModelMock            = $this->createMock(ProcessosSeletivosModel::class);
        $this->logsModelMock                 = $this->createMock(LogsModel::class);
        $this->classificacaoServiceMock     = $this->createMock(ClassificacaoService::class);

        $this->service = new DisparoClassificacaoService(
            $this->usuarioModelMock,
            $this->situacaoProcessosModelMock,
            $this->processosModelMock,
            $this->logsModelMock,
            $this->classificacaoServiceMock,
        );
    }

    /**
     * Cria um objeto stdClass simulando um registro de usuario.
     */
    private function usuarioAdmin(): \stdClass
    {
        $usuario = new \stdClass();
        $usuario->pk_id_user         = 1;
        $usuario->ds_nome_user       = 'Administrador Teste';
        $usuario->ds_administrador  = '1';
        return $usuario;
    }

    /**
     * Cria um objeto stdClass simulando um registro de situacao existente.
     */
    private function situacaoExistente(): \stdClass
    {
        $situacao = new \stdClass();
        $situacao->pk_id_situacao_processo = 10;
        $situacao->fk_id_processo          = 5;
        $situacao->nm_id_situacao          = 2;
        return $situacao;
    }

    /**
     * Cria um objeto stdClass simulando um processo seletivo.
     */
    private function processo(int $situacao = 2): \stdClass
    {
        $processo = new \stdClass();
        $processo->pk_id_processo = 5;
        $processo->nm_situacao     = $situacao;
        return $processo;
    }

    public function testDisparoPreliminarAtualizaSituacaoEReprocessa(): void
    {
        $this->usuarioModelMock
            ->method('find')
            ->with(1)
            ->willReturn($this->usuarioAdmin());

        $this->situacaoProcessosModelMock
            ->method('porProcesso')
            ->with(5)
            ->willReturn($this->situacaoExistente());

        $this->situacaoProcessosModelMock
            ->expects($this->once())
            ->method('update')
            ->with(
                10,
                $this->callback(function (array $data) {
                    return $data['fk_id_processo'] === 5
                        && $data['nm_id_situacao'] === 3
                        && !empty($data['dt_ultima_alteracao']);
                })
            );

        $this->processosModelMock
            ->method('find')
            ->with(5)
            ->willReturn($this->processo());

        $this->processosModelMock
            ->expects($this->once())
            ->method('update')
            ->with(5, ['nm_situacao' => 3]);

        $this->classificacaoServiceMock
            ->expects($this->once())
            ->method('reprocessar')
            ->with(5, 7);

        $this->logsModelMock
            ->expects($this->once())
            ->method('insert')
            ->with($this->callback(function (array $data) {
                return $data['id_usuario'] === 1
                    && $data['ds_usuario'] === 'Administrador Teste'
                    && $data['ds_acao'] === 'DISPARO DE CLASSIFICACAO'
                    && $data['ds_tabela'] === 'tb_situacao_processos'
                    && str_contains((string) $data['ds_mensagem'], 'PRELIMINAR')
                    && str_contains((string) $data['ds_mensagem'], 'processo 5')
                    && str_contains((string) $data['ds_mensagem'], 'cargo 7');
            }));

        $this->service->disparar(5, 7, 'preliminar', 1);

        $this->assertTrue(true);
    }

    public function testDisparoHomologacaoInsereSituacaoQuandoNaoExiste(): void
    {
        $this->usuarioModelMock
            ->method('find')
            ->with(1)
            ->willReturn($this->usuarioAdmin());

        $this->situacaoProcessosModelMock
            ->method('porProcesso')
            ->with(5)
            ->willReturn(null);

        $this->situacaoProcessosModelMock
            ->expects($this->once())
            ->method('insert')
            ->with($this->callback(function (array $data) {
                return $data['fk_id_processo'] === 5
                    && $data['nm_id_situacao'] === 4
                    && !empty($data['dt_ultima_alteracao']);
            }));

        $this->processosModelMock
            ->method('find')
            ->with(5)
            ->willReturn($this->processo(4));

        // situacao ja igual, nao deve atualizar tb_processos
        $this->processosModelMock
            ->method('update')
            ->with(5, $this->anything())
            ->willReturnCallback(function () {
                $this->fail('tb_processos.nm_situacao ja estava igual; update nao deveria ser chamado');
            });

        $this->classificacaoServiceMock
            ->expects($this->once())
            ->method('reprocessar')
            ->with(5, 7);

        $this->logsModelMock
            ->expects($this->once())
            ->method('insert')
            ->with($this->callback(function (array $data) {
                return str_contains((string) $data['ds_mensagem'], 'HOMOLOGACAO');
            }));

        $this->service->disparar(5, 7, 'homologacao', 1);

        $this->assertTrue(true);
    }

    public function testDisparoComTipoInvalidoLancaExcecao(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Tipo de classificacao invalido');

        $this->service->disparar(5, 7, 'inexistente', 1);
    }

    public function testDisparoPorNaoAdministradorLancaExcecao(): void
    {
        $usuario = new \stdClass();
        $usuario->ds_administrador = '0';

        $this->usuarioModelMock
            ->method('find')
            ->with(2)
            ->willReturn($usuario);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Apenas administradores podem disparar');

        $this->service->disparar(5, 7, 'preliminar', 2);
    }

    public function testDisparoComUsuarioInexistenteLancaExcecao(): void
    {
        $this->usuarioModelMock
            ->method('find')
            ->with(99)
            ->willReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Apenas administradores podem disparar');

        $this->service->disparar(5, 7, 'preliminar', 99);
    }
}
