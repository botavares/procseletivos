<?php

namespace Tests\Admin\Services\Classificacao;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\Admin\Classificacao\DTO\DesempateConfigDTO;

/**
 * @internal
 */
final class DesempateConfigDTOTest extends CIUnitTestCase
{
    public function testChaveScorePontuacaoTotal(): void
    {
        $dto = new DesempateConfigDTO(1, 1, 'PONTUACAO_TOTAL', null, 'DESC', null, 'Pontuação Total');
        $this->assertSame('PONTUACAO_TOTAL', $dto->chaveScore());
    }

    public function testChaveScoreExperienciaEspecifica(): void
    {
        $dto = new DesempateConfigDTO(1, 1, 'EXPERIENCIA_ESPECIFICA', 7, 'DESC', null, 'Experiência GIS');
        $this->assertSame('EXPERIENCIA_ESPECIFICA_7', $dto->chaveScore());
    }

    public function testChaveScoreEscolaridadeEspecifica(): void
    {
        $dto = new DesempateConfigDTO(1, 1, 'ESCOLARIDADE_ESPECIFICA', 3, 'ASC', null, 'Escolaridade');
        $this->assertSame('ESCOLARIDADE_ESPECIFICA_3', $dto->chaveScore());
    }

    public function testChaveScoreIdade(): void
    {
        $dto = new DesempateConfigDTO(1, 1, 'IDADE', null, 'ASC', null, 'Idade');
        $this->assertSame('IDADE', $dto->chaveScore());
    }

    public function testChaveScorePne(): void
    {
        $dto = new DesempateConfigDTO(1, 1, 'PNE', null, 'DESC', null, 'PNE');
        $this->assertSame('PNE', $dto->chaveScore());
    }

    public function testChaveScoreTipoDesconhecidoLancaExcecao(): void
    {
        $dto = new DesempateConfigDTO(1, 1, 'TIPO_INVALIDO', null, 'DESC', null, 'Inválido');

        $this->expectException(\InvalidArgumentException::class);
        $dto->chaveScore();
    }
}