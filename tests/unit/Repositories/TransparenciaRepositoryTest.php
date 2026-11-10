<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Repositories\TransparenciaRepository;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\ResultInterface;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class TransparenciaRepositoryTest extends CIUnitTestCase
{
    private TransparenciaRepository $repository;
    private $dbMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbMock = $this->createMock(BaseConnection::class);
        $this->repository = new TransparenciaRepository($this->dbMock);
    }

    public function testListarClassificacaoAplicaFiltroSecretariaQuandoProcessoECargoInformados(): void
    {
        $builderMock = $this->getMockBuilder(BaseBuilder::class)
            ->setConstructorArgs(['tb_classificacao c', $this->dbMock])
            ->onlyMethods([
                'select',
                'join',
                'where',
                'like',
                'countAllResults',
                'orderBy',
                'limit',
                'get',
            ])
            ->getMock();

        $resultMock = $this->createMock(ResultInterface::class);
        $resultMock->method('getResult')->willReturn([]);

        $wheres = [];

        // Configura encadeamento fluente: todos retornam o proprio builder.
        $builderMock->method('select')->willReturn($builderMock);
        $builderMock->method('join')->willReturn($builderMock);
        $builderMock->method('where')->willReturnCallback(function (...$args) use ($builderMock, &$wheres) {
            $wheres[] = $args;
            return $builderMock;
        });
        $builderMock->method('like')->willReturn($builderMock);
        $builderMock->method('orderBy')->willReturn($builderMock);
        $builderMock->method('limit')->willReturn($builderMock);
        $builderMock->method('get')->willReturn($resultMock);
        $builderMock->method('countAllResults')->with(false)->willReturn(0);

        $this->dbMock
            ->method('table')
            ->with('tb_classificacao c')
            ->willReturn($builderMock);

        $this->repository->listarClassificacao(
            1,
            10,
            7,
            '',
            false,
            [],
            [],
            5
        );

        $chavesWhere = array_map(static fn (array $args) => $args[0], $wheres);

        $this->assertContains('c.fk_id_secretaria = p.fk_id_secretaria', $chavesWhere);
    }

    public function testListarClassificacaoNaoAplicaFiltroSecretariaSemCargo(): void
    {
        $builderMock = $this->getMockBuilder(BaseBuilder::class)
            ->setConstructorArgs(['tb_classificacao c', $this->dbMock])
            ->onlyMethods([
                'select',
                'join',
                'where',
                'like',
                'countAllResults',
                'orderBy',
                'limit',
                'get',
            ])
            ->getMock();

        $resultMock = $this->createMock(ResultInterface::class);
        $resultMock->method('getResult')->willReturn([]);

        $wheres = [];

        $builderMock->method('select')->willReturn($builderMock);
        $builderMock->method('join')->willReturn($builderMock);
        $builderMock->method('where')->willReturnCallback(function (...$args) use ($builderMock, &$wheres) {
            $wheres[] = $args;
            return $builderMock;
        });
        $builderMock->method('like')->willReturn($builderMock);
        $builderMock->method('orderBy')->willReturn($builderMock);
        $builderMock->method('limit')->willReturn($builderMock);
        $builderMock->method('get')->willReturn($resultMock);
        $builderMock->method('countAllResults')->with(false)->willReturn(0);

        $this->dbMock
            ->method('table')
            ->with('tb_classificacao c')
            ->willReturn($builderMock);

        $this->repository->listarClassificacao(
            1,
            10,
            0,
            '',
            false,
            [],
            [],
            5
        );

        $chavesWhere = array_map(static fn (array $args) => $args[0], $wheres);

        $this->assertNotContains('c.fk_id_secretaria = p.fk_id_secretaria', $chavesWhere);
    }

}
