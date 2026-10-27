<?php

declare(strict_types=1);

namespace Tests\Admin\Enums;

use App\Enums\Admin\TiposDocumentosEnum;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class TiposDocumentosEnumTest extends CIUnitTestCase
{
    public function testValoresEnum(): void
    {
        $this->assertSame(1, TiposDocumentosEnum::EDITAL_ABERTURA->value);
        $this->assertSame(2, TiposDocumentosEnum::RETIFICACAO->value);
        $this->assertSame(3, TiposDocumentosEnum::RESULTADO_PARCIAL->value);
        $this->assertSame(4, TiposDocumentosEnum::HOMOLOGACAO->value);
        $this->assertSame(5, TiposDocumentosEnum::EDITAL_CONVOCACAO->value);
        $this->assertSame(6, TiposDocumentosEnum::COMUNICADO->value);
    }

    public function testLabels(): void
    {
        $this->assertSame('Edital de Abertura', TiposDocumentosEnum::EDITAL_ABERTURA->label());
        $this->assertSame('Retificação', TiposDocumentosEnum::RETIFICACAO->label());
        $this->assertSame('Resultado Parcial', TiposDocumentosEnum::RESULTADO_PARCIAL->label());
        $this->assertSame('Homologação', TiposDocumentosEnum::HOMOLOGACAO->label());
        $this->assertSame('Edital de Convocação', TiposDocumentosEnum::EDITAL_CONVOCACAO->label());
        $this->assertSame('Comunicado', TiposDocumentosEnum::COMUNICADO->label());
    }

    public function testTryFromValido(): void
    {
        $enum = TiposDocumentosEnum::tryFrom(2);
        $this->assertNotNull($enum);
        $this->assertSame(TiposDocumentosEnum::RETIFICACAO, $enum);
    }

    public function testTryFromInvalido(): void
    {
        $this->assertNull(TiposDocumentosEnum::tryFrom(99));
    }
}
