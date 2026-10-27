<?php

declare(strict_types=1);

namespace Tests\Unit\Enums\Admin;

use App\Enums\Admin\ProrrogacaoProcessoEnum;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ProrrogacaoProcessoEnumTest extends CIUnitTestCase
{
    public function testValoresEnum(): void
    {
        $this->assertSame(0, ProrrogacaoProcessoEnum::NAO->value);
        $this->assertSame(1, ProrrogacaoProcessoEnum::SIM->value);
    }

    public function testLabels(): void
    {
        $this->assertSame('Não', ProrrogacaoProcessoEnum::NAO->label());
        $this->assertSame('Sim', ProrrogacaoProcessoEnum::SIM->label());
    }
}
