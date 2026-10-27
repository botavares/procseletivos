<?php

declare(strict_types=1);

namespace Tests\Admin\Validation;

use App\Validation\ProcessosDocumentosRules;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ProcessosDocumentosRulesTest extends CIUnitTestCase
{
    private ProcessosDocumentosRules $rules;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rules = new ProcessosDocumentosRules();
    }

    public function testApenasNumerosAceitaDigitos(): void
    {
        $this->assertTrue($this->rules->apenas_numeros('12345'));
    }

    public function testApenasNumerosRejeitaLetras(): void
    {
        $this->assertFalse($this->rules->apenas_numeros('123a'));
    }

    public function testApenasNumerosRejeitaVazio(): void
    {
        $this->assertFalse($this->rules->apenas_numeros(''));
        $this->assertFalse($this->rules->apenas_numeros(null));
    }

    public function testAnoAtualOuFuturoAceitaAnoAtual(): void
    {
        $this->assertTrue($this->rules->ano_atual_ou_futuro((string) date('Y')));
    }

    public function testAnoAtualOuFuturoAceitaAnoFuturo(): void
    {
        $this->assertTrue($this->rules->ano_atual_ou_futuro((string) (date('Y') + 1)));
    }

    public function testAnoAtualOuFuturoRejeitaAnoPassado(): void
    {
        $this->assertFalse($this->rules->ano_atual_ou_futuro((string) (date('Y') - 1)));
    }

    public function testArquivoPdfCondicionalAceitaPdfParaEditalAbertura(): void
    {
        $data = ['nm_tipo' => '1'];
        $this->assertTrue($this->rules->arquivo_pdf_condicional('doc.pdf', null, $data));
    }

    public function testArquivoPdfCondicionalRejeitaDocParaEditalAbertura(): void
    {
        $data = ['nm_tipo' => '1'];
        $this->assertFalse($this->rules->arquivo_pdf_condicional('doc.docx', null, $data));
    }

    public function testArquivoPdfCondicionalRejeitaArquivoParaResultadoParcial(): void
    {
        $data = ['nm_tipo' => '3'];
        $this->assertFalse($this->rules->arquivo_pdf_condicional('doc.pdf', null, $data));
    }

    public function testArquivoPdfCondicionalAceitaVazioParaResultadoParcial(): void
    {
        $data = ['nm_tipo' => '3'];
        $this->assertTrue($this->rules->arquivo_pdf_condicional('', null, $data));
    }

    public function testVersaoVigenteRegraRejeitaValorParaRetificacao(): void
    {
        $data = ['nm_tipo' => '2'];
        $this->assertFalse($this->rules->versao_vigente_regra('5', null, $data));
    }

    public function testVersaoVigenteRegraAceitaZeroParaRetificacao(): void
    {
        $data = ['nm_tipo' => '2'];
        $this->assertTrue($this->rules->versao_vigente_regra('0', null, $data));
    }

    public function testVersaoVigenteRegraAceitaValorParaOutroTipo(): void
    {
        $data = ['nm_tipo' => '1'];
        $this->assertTrue($this->rules->versao_vigente_regra('5', null, $data));
    }

    public function testDocumentoRetificadoRegraRejeitaValorParaNaoRetificacao(): void
    {
        $data = ['nm_tipo' => '1'];
        $this->assertFalse($this->rules->documento_retificado_regra('5', null, $data));
    }

    public function testDocumentoRetificadoRegraAceitaZeroParaNaoRetificacao(): void
    {
        $data = ['nm_tipo' => '1'];
        $this->assertTrue($this->rules->documento_retificado_regra('0', null, $data));
    }

    public function testDocumentoRetificadoRegraAceitaValorParaRetificacao(): void
    {
        $data = ['nm_tipo' => '2'];
        $this->assertTrue($this->rules->documento_retificado_regra('5', null, $data));
    }
}
