<?php

declare(strict_types=1);

namespace Tests\Admin\Controllers;

use App\Controllers\Admin\ProcessosDocumentos;
use App\Services\Admin\ProcessosDocumentos\ProcessosDocumentosService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ProcessosDocumentosControllerTest extends CIUnitTestCase
{
    public function testControllerInstanciaComService(): void
    {
        $serviceMock = $this->createMock(ProcessosDocumentosService::class);
        $controller  = new ProcessosDocumentos($serviceMock);

        $ref = new \ReflectionClass($controller);
        $prop = $ref->getProperty('service');
        $prop->setAccessible(true);

        $this->assertSame($serviceMock, $prop->getValue($controller));
    }

    public function testControllerInstanciaSemServiceUsaServicePadrao(): void
    {
        $controller = new ProcessosDocumentos();
        $this->assertInstanceOf(ProcessosDocumentos::class, $controller);
    }
}
