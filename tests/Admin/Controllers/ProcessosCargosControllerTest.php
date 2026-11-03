<?php

declare(strict_types=1);

namespace Tests\Admin\Controllers;

use App\Controllers\Admin\ProcessosCargos;
use App\Services\Admin\ProcessosCargos\ProcessosCargosService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ProcessosCargosControllerTest extends CIUnitTestCase
{
    public function testControllerPodeSerInstanciado(): void
    {
        $controller = new ProcessosCargos();
        $this->assertInstanceOf(ProcessosCargos::class, $controller);
    }
}
