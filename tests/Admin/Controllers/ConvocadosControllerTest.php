<?php

declare(strict_types=1);

namespace Tests\Admin\Controllers;

use App\Controllers\Admin\Convocados;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ConvocadosControllerTest extends CIUnitTestCase
{
    public function testControllerPodeSerInstanciado(): void
    {
        $controller = new Convocados();
        $this->assertInstanceOf(Convocados::class, $controller);
    }
}
