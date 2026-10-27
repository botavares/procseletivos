<?php

namespace Tests\Admin\Services\Classificacao;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\Admin\Classificacao\PontuacaoCalculatorService;
use CodeIgniter\Database\BaseConnection;

/**
 * @internal
 */
final class PontuacaoCalculatorServiceTest extends CIUnitTestCase
{
    private PontuacaoCalculatorService $service;
    private $dbMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbMock = $this->createMock(BaseConnection::class);
        $this->service = new PontuacaoCalculatorService($this->dbMock);
    }

    /**
     * Testa que pontuação abaixo do teto é preservada.
     */
    public function testAplicarTetoPreservaPontuacaoAbaixoDoLimite(): void
    {
        $ref = new \ReflectionMethod($this->service, 'aplicarTeto');
        $ref->setAccessible(true);

        $resultado = $ref->invoke($this->service, 50.0, 100.0);
        $this->assertSame(50.0, $resultado);
    }

    /**
     * Testa que pontuação acima do teto é limitada.
     */
    public function testAplicarTetoLimitaPontuacaoAcimaDoLimite(): void
    {
        $ref = new \ReflectionMethod($this->service, 'aplicarTeto');
        $ref->setAccessible(true);

        $resultado = $ref->invoke($this->service, 150.0, 100.0);
        $this->assertSame(100.0, $resultado);
    }

    /**
     * Testa que pontuação sem teto é preservada integralmente.
     */
    public function testAplicarTetoSemLimitePreservaPontuacao(): void
    {
        $ref = new \ReflectionMethod($this->service, 'aplicarTeto');
        $ref->setAccessible(true);

        $resultado = $ref->invoke($this->service, 200.0, null);
        $this->assertSame(200.0, $resultado);
    }

    /**
     * Testa que teto zero não é aplicado.
     */
    public function testAplicarTetoZeroNaoLimita(): void
    {
        $ref = new \ReflectionMethod($this->service, 'aplicarTeto');
        $ref->setAccessible(true);

        $resultado = $ref->invoke($this->service, 50.0, 0.0);
        $this->assertSame(50.0, $resultado);
    }
}