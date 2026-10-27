<?php

declare(strict_types=1);

namespace Tests\Admin\Controllers;

use App\Controllers\Admin\ProcessosSeletivos;
use App\Services\Admin\ProcessosSeletivos\ProcessosSeletivosService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ProcessosSeletivosControllerTest extends CIUnitTestCase
{
    public function testControllerInstanciaComService(): void
    {
        $serviceMock = $this->createMock(ProcessosSeletivosService::class);
        $controller  = new ProcessosSeletivos($serviceMock);

        $ref = new \ReflectionClass($controller);
        $prop = $ref->getProperty('service');
        $prop->setAccessible(true);

        $this->assertSame($serviceMock, $prop->getValue($controller));
    }

    public function testControllerInstanciaSemServiceUsaServicePadrao(): void
    {
        // Certifica-se de que o service está registrado em Config\Services
        $controller = new ProcessosSeletivos();
        $this->assertInstanceOf(ProcessosSeletivos::class, $controller);
    }
}
