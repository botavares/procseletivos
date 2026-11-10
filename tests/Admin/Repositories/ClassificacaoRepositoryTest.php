<?php

namespace Tests\Admin\Repositories;

use CodeIgniter\Test\CIUnitTestCase;
use App\Repositories\Admin\ClassificacaoRepository;
use App\Models\Admin\ClassificacaoModel;

/**
 * @internal
 */
final class ClassificacaoRepositoryTest extends CIUnitTestCase
{
    private ClassificacaoRepository $repository;
    private $modelMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->modelMock = $this->createMock(ClassificacaoModel::class);
        $this->repository = new ClassificacaoRepository($this->modelMock);
    }

    public function testListarPorProcessoCargoDelegaParaModel(): void
    {
        $esperado = [['fk_id_candidato' => 1, 'ds_posicao' => 1]];
        $this->modelMock
            ->method('listarClassificacao')
            ->with(10, 20, null)
            ->willReturn($esperado);

        $resultado = $this->repository->listarPorProcessoCargo(10, 20);

        $this->assertSame($esperado, $resultado);
    }

    public function testListarPorEditalCargoDelegaParaModel(): void
    {
        $esperado = [['fk_id_candidato' => 1, 'ds_posicao' => 1]];
        $this->modelMock
            ->method('listarClassificacao')
            ->with(5, 20, null)
            ->willReturn($esperado);

        $resultado = $this->repository->listarPorEditalCargo(5, 20);

        $this->assertSame($esperado, $resultado);
    }
}