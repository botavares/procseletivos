<?php

declare(strict_types=1);

namespace Tests\Unit\Enums\Admin;

use App\Enums\Admin\SituacaoProcessosEnum;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class SituacaoProcessosEnumTest extends CIUnitTestCase
{
    public function testValoresEnum(): void
    {
        $this->assertSame(1, SituacaoProcessosEnum::INSCRICOES_ABERTAS->value);
        $this->assertSame(2, SituacaoProcessosEnum::INSCRICOES_ENCERRADAS->value);
        $this->assertSame(3, SituacaoProcessosEnum::RESULTADO_PUBLICADO->value);
        $this->assertSame(4, SituacaoProcessosEnum::VIGENTE->value);
        $this->assertSame(5, SituacaoProcessosEnum::ENCERRADO->value);
    }

    public function testLabels(): void
    {
        $this->assertSame('Inscrições Abertas', SituacaoProcessosEnum::INSCRICOES_ABERTAS->label());
        $this->assertSame('Inscrições Encerradas', SituacaoProcessosEnum::INSCRICOES_ENCERRADAS->label());
        $this->assertSame('Resultado Publicado', SituacaoProcessosEnum::RESULTADO_PUBLICADO->label());
        $this->assertSame('Vigente', SituacaoProcessosEnum::VIGENTE->label());
        $this->assertSame('Encerrado', SituacaoProcessosEnum::ENCERRADO->label());
    }

    public function testTryFromValorValido(): void
    {
        $enum = SituacaoProcessosEnum::tryFrom(3);
        $this->assertInstanceOf(SituacaoProcessosEnum::class, $enum);
        $this->assertSame(SituacaoProcessosEnum::RESULTADO_PUBLICADO, $enum);
    }

    public function testTryFromValorInvalido(): void
    {
        $this->assertNull(SituacaoProcessosEnum::tryFrom(99));
    }
}
