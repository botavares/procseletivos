<?php

namespace Tests\Admin\Repositories;

use CodeIgniter\Test\CIUnitTestCase;
use App\Repositories\Admin\CandidatoRepository;
use App\Models\Admin\CandidatosModel;

/**
 * @internal
 */
final class CandidatoRepositoryTest extends CIUnitTestCase
{
    private CandidatoRepository $repository;
    private $modelMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->modelMock = $this->createMock(CandidatosModel::class);
        $this->repository = new CandidatoRepository($this->modelMock);
    }

    public function testBuscarPorEditalCargoDelegaParaModel(): void
    {
        $esperado = [['pk_id_cadastrado' => 1]];
        $this->modelMock->method('getCandidatosPorEditalCargo')
            ->with(10, 20)
            ->willReturn($esperado);

        $resultado = $this->repository->buscarPorEditalCargo(10, 20);

        $this->assertSame($esperado, $resultado);
    }

    public function testBuscarPorCpfDelegaParaModel(): void
    {
        $esperado = (object)['ds_nome' => 'João'];
        $this->modelMock->method('listarCandidatoCpf')
            ->with('12345678901')
            ->willReturn($esperado);

        $resultado = $this->repository->buscarPorCpf('12345678901');

        $this->assertSame($esperado, $resultado);
    }

    public function testBuscarPorIdDelegaParaModel(): void
    {
        $esperado = (object)['pk_id_cadastrado' => 5];
        $this->modelMock->method('listarCandidatoId')
            ->with(10, 20, 5)
            ->willReturn($esperado);

        $resultado = $this->repository->buscarPorId(10, 20, 5);

        $this->assertSame($esperado, $resultado);
    }

    public function testContarPorAnoDelegaParaModel(): void
    {
        $this->modelMock->method('contarCandidatos')
            ->with(2026)
            ->willReturn(150);

        $resultado = $this->repository->contarPorAno(2026);

        $this->assertSame(150, $resultado);
    }

    public function testBuscarExperienciasDelegaParaModel(): void
    {
        $esperado = [['ds_experiencia' => 'Cargo Anterior']];
        $this->modelMock->method('listarExperienciaCandidato')
            ->with(10, 20, 5)
            ->willReturn($esperado);

        $resultado = $this->repository->buscarExperiencias(10, 20, 5);

        $this->assertSame($esperado, $resultado);
    }
}