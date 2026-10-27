<?php

declare(strict_types=1);

namespace Tests\Unit\Validation;

use App\Validation\ProcessosSeletivosRules;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ProcessosSeletivosRulesTest extends CIUnitTestCase
{
    private ProcessosSeletivosRules $rules;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rules = new ProcessosSeletivosRules();
    }

    public function testApenasNumerosComValorValido(): void
    {
        $this->assertTrue($this->rules->apenas_numeros('12345', '', []));
        $this->assertTrue($this->rules->apenas_numeros('0', '', []));
    }

    public function testApenasNumerosComValorInvalido(): void
    {
        $this->assertFalse($this->rules->apenas_numeros('12a45', '', []));
        $this->assertFalse($this->rules->apenas_numeros(' 123', '', []));
        $this->assertFalse($this->rules->apenas_numeros('', '', []));
        $this->assertFalse($this->rules->apenas_numeros(null, '', []));
    }

    public function testAnoAtualOuFuturoComAnoAtual(): void
    {
        $anoAtual = (string) date('Y');
        $this->assertTrue($this->rules->ano_atual_ou_futuro($anoAtual, '', []));
    }

    public function testAnoAtualOuFuturoComAnoFuturo(): void
    {
        $anoFuturo = (string) ((int) date('Y') + 1);
        $this->assertTrue($this->rules->ano_atual_ou_futuro($anoFuturo, '', []));
    }

    public function testAnoAtualOuFuturoComAnoPassado(): void
    {
        $anoPassado = (string) ((int) date('Y') - 1);
        $this->assertFalse($this->rules->ano_atual_ou_futuro($anoPassado, '', []));
    }

    public function testAnoAtualOuFuturoComValorInvalido(): void
    {
        $this->assertFalse($this->rules->ano_atual_ou_futuro('', '', []));
        $this->assertFalse($this->rules->ano_atual_ou_futuro(null, '', []));
        $this->assertFalse($this->rules->ano_atual_ou_futuro('abc', '', []));
    }

    public function testDataFimMaiorIgualInicioComDataFimIgual(): void
    {
        $this->assertTrue($this->rules->data_fim_maior_igual_inicio('15/10/2025', 'dt_inicio', ['dt_inicio' => '15/10/2025']));
    }

    public function testDataFimMaiorIgualInicioComDataFimMaior(): void
    {
        $this->assertTrue($this->rules->data_fim_maior_igual_inicio('20/10/2025', 'dt_inicio', ['dt_inicio' => '15/10/2025']));
    }

    public function testDataFimMaiorIgualInicioComDataFimMenor(): void
    {
        $this->assertFalse($this->rules->data_fim_maior_igual_inicio('10/10/2025', 'dt_inicio', ['dt_inicio' => '15/10/2025']));
    }

    public function testDataFimMaiorIgualInicioComParametroAusente(): void
    {
        $this->assertFalse($this->rules->data_fim_maior_igual_inicio('20/10/2025', 'dt_inicio', []));
    }

    public function testDataFimMaiorIgualInicioComValorVazio(): void
    {
        $this->assertFalse($this->rules->data_fim_maior_igual_inicio('', 'dt_inicio', ['dt_inicio' => '15/10/2025']));
        $this->assertFalse($this->rules->data_fim_maior_igual_inicio(null, 'dt_inicio', ['dt_inicio' => '15/10/2025']));
    }
}
