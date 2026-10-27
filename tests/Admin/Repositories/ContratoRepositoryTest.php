<?php

namespace Tests\Admin\Repositories;

use CodeIgniter\Test\CIUnitTestCase;
use App\Repositories\Admin\ContratoRepository;
use App\Models\Admin\DadosContratosModel;

/**
 * @internal
 */
final class ContratoRepositoryTest extends CIUnitTestCase
{
    private ContratoRepository $repository;
    private $modelMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->modelMock = $this->createMock(DadosContratosModel::class);
        $this->repository = new ContratoRepository($this->modelMock);
    }

    public function testListarTodosDelegaParaModel(): void
    {
        $esperado = [(object)['pk_id_contrato' => 1]];
        $this->modelMock->method('getDadosContrato')
            ->willReturn($esperado);

        $resultado = $this->repository->listarTodos();

        $this->assertSame($esperado, $resultado);
    }

    public function testBuscarPorIdDelegaParaModel(): void
    {
        $esperado = (object)['pk_id_contrato' => 1];
        $this->modelMock->method('getDadoContrato')
            ->with(1)
            ->willReturn($esperado);

        $resultado = $this->repository->buscarPorId(1);

        $this->assertSame($esperado, $resultado);
    }

    public function testBuscarPorCandidatoDelegaParaModel(): void
    {
        $esperado = [(object)['pk_id_contrato' => 1]];
        $this->modelMock->method('getContratosPorCandidato')
            ->with(42)
            ->willReturn($esperado);

        $resultado = $this->repository->buscarPorCandidato(42);

        $this->assertSame($esperado, $resultado);
    }

    public function testInserirDelegaParaModel(): void
    {
        $dados = ['fk_id_candidato' => 42];
        $this->modelMock->method('insert')
            ->with($dados)
            ->willReturn(true);

        $resultado = $this->repository->inserir($dados);

        $this->assertTrue($resultado);
    }

    public function testAtualizarDelegaParaModel(): void
    {
        $dados = ['ds_status' => '2'];
        $this->modelMock->method('update')
            ->with(1, $dados)
            ->willReturn(true);

        $resultado = $this->repository->atualizar(1, $dados);

        $this->assertTrue($resultado);
    }

    public function testBuscarAtivosDelegaParaModel(): void
    {
        $esperado = [(object)['pk_id_contrato' => 1]];
        $this->modelMock->method('getContratosAtivos')
            ->willReturn($esperado);

        $resultado = $this->repository->buscarAtivos();

        $this->assertSame($esperado, $resultado);
    }

    public function testObterUltimoIdDelegaParaModel(): void
    {
        $this->modelMock->method('getInsertID')
            ->willReturn(99);

        $resultado = $this->repository->obterUltimoId();

        $this->assertSame(99, $resultado);
    }
}