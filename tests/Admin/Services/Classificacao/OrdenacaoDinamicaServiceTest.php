<?php

namespace Tests\Admin\Services\Classificacao;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\Admin\Classificacao\OrdenacaoDinamicaService;
use App\Services\Admin\Classificacao\DTO\DesempateConfigDTO;
use App\Services\Admin\Classificacao\DTO\ResultadoClassificacaoDTO;

/**
 * @internal
 */
final class OrdenacaoDinamicaServiceTest extends CIUnitTestCase
{
    private OrdenacaoDinamicaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OrdenacaoDinamicaService();
    }

    /**
     * Testa ordenação por pontuação total DESC (maior primeiro).
     */
    public function testOrdenacaoPorPontuacaoTotalDesc(): void
    {
        $candidato1 = new ResultadoClassificacaoDTO(1, 'Ana', 'Cargo A', 'Edital 1', '1990-01-01', 10, 5, 0, 0, 0, 0, 0, 100);
        $candidato1->scores = ['PONTUACAO_TOTAL' => 100];

        $candidato2 = new ResultadoClassificacaoDTO(2, 'Bruno', 'Cargo A', 'Edital 1', '1985-01-01', 20, 10, 0, 0, 0, 0, 0, 150);
        $candidato2->scores = ['PONTUACAO_TOTAL' => 150];

        $candidato3 = new ResultadoClassificacaoDTO(3, 'Carla', 'Cargo A', 'Edital 1', '1995-01-01', 5, 2, 0, 0, 0, 0, 0, 80);
        $candidato3->scores = ['PONTUACAO_TOTAL' => 80];

        $config = [
            new DesempateConfigDTO(1, 1, 'PONTUACAO_TOTAL', null, 'DESC', null, 'Pontuação Total'),
        ];

        $resultado = $this->service->ordenar([$candidato1, $candidato2, $candidato3], $config);

        $this->assertSame(2, $resultado[0]->fk_id_candidato); // Bruno 150
        $this->assertSame(1, $resultado[1]->fk_id_candidato); // Ana 100
        $this->assertSame(3, $resultado[2]->fk_id_candidato); // Carla 80
    }

    /**
     * Testa desempate por experiências quando pontuação total é igual.
     */
    public function testDesempatePorExperiencias(): void
    {
        $candidato1 = new ResultadoClassificacaoDTO(1, 'Ana', 'Cargo A', 'Edital 1', '1990-01-01', 30, 0, 0, 0, 0, 0, 0, 100);
        $candidato1->scores = ['PONTUACAO_TOTAL' => 100, 'PONTUACAO_EXPERIENCIAS' => 30];

        $candidato2 = new ResultadoClassificacaoDTO(2, 'Bruno', 'Cargo A', 'Edital 1', '1985-01-01', 20, 0, 0, 0, 0, 0, 0, 100);
        $candidato2->scores = ['PONTUACAO_TOTAL' => 100, 'PONTUACAO_EXPERIENCIAS' => 20];

        $config = [
            new DesempateConfigDTO(1, 1, 'PONTUACAO_TOTAL', null, 'DESC', null, 'Pontuação Total'),
            new DesempateConfigDTO(2, 2, 'PONTUACAO_EXPERIENCIAS', null, 'DESC', null, 'Experiências'),
        ];

        $resultado = $this->service->ordenar([$candidato2, $candidato1], $config);

        $this->assertSame(1, $resultado[0]->fk_id_candidato); // Ana tem mais experiência
        $this->assertSame(2, $resultado[1]->fk_id_candidato);
    }

    /**
     * Testa critério de idade ASC (mais velho primeiro).
     */
    public function testDesempatePorIdadeAscMaisVelhoPrimeiro(): void
    {
        $candidato1 = new ResultadoClassificacaoDTO(1, 'Ana', 'Cargo A', 'Edital 1', '1980-01-01', 0, 0, 0, 0, 0, 0, 0, 100);
        $candidato1->scores = ['PONTUACAO_TOTAL' => 100, 'IDADE' => '1980-01-01'];

        $candidato2 = new ResultadoClassificacaoDTO(2, 'Bruno', 'Cargo A', 'Edital 1', '1990-01-01', 0, 0, 0, 0, 0, 0, 0, 100);
        $candidato2->scores = ['PONTUACAO_TOTAL' => 100, 'IDADE' => '1990-01-01'];

        $config = [
            new DesempateConfigDTO(1, 1, 'PONTUACAO_TOTAL', null, 'DESC', null, 'Pontuação Total'),
            new DesempateConfigDTO(2, 2, 'IDADE', null, 'ASC', null, 'Idade'),
        ];

        $resultado = $this->service->ordenar([$candidato2, $candidato1], $config);

        $this->assertSame(1, $resultado[0]->fk_id_candidato); // Ana é mais velha
        $this->assertSame(2, $resultado[1]->fk_id_candidato);
    }

    /**
     * Testa critério PNE DESC (com PNE vem primeiro).
     */
    public function testDesempatePorPneDesc(): void
    {
        $candidato1 = new ResultadoClassificacaoDTO(1, 'Ana', 'Cargo A', 'Edital 1', '1990-01-01', 0, 0, 0, 0, 0, 0, 0, 100);
        $candidato1->scores = ['PONTUACAO_TOTAL' => 100, 'PNE' => 1];
        $candidato1->ds_possui_pne = 1;

        $candidato2 = new ResultadoClassificacaoDTO(2, 'Bruno', 'Cargo A', 'Edital 1', '1990-01-01', 0, 0, 0, 0, 0, 0, 0, 100);
        $candidato2->scores = ['PONTUACAO_TOTAL' => 100, 'PNE' => 0];
        $candidato2->ds_possui_pne = 0;

        $config = [
            new DesempateConfigDTO(1, 1, 'PONTUACAO_TOTAL', null, 'DESC', null, 'Pontuação Total'),
            new DesempateConfigDTO(2, 2, 'PNE', null, 'DESC', null, 'PNE'),
        ];

        $resultado = $this->service->ordenar([$candidato2, $candidato1], $config);

        $this->assertSame(1, $resultado[0]->fk_id_candidato); // Ana tem PNE
        $this->assertSame(2, $resultado[1]->fk_id_candidato);
    }

    /**
     * Testa atribuição de posições após ordenação.
     */
    public function testAtribuicaoDePosicoes(): void
    {
        $candidato1 = new ResultadoClassificacaoDTO(1, 'Ana', 'Cargo A', 'Edital 1', '1990-01-01', 0, 0, 0, 0, 0, 0, 0, 50);
        $candidato1->scores = ['PONTUACAO_TOTAL' => 50];

        $candidato2 = new ResultadoClassificacaoDTO(2, 'Bruno', 'Cargo A', 'Edital 1', '1990-01-01', 0, 0, 0, 0, 0, 0, 0, 100);
        $candidato2->scores = ['PONTUACAO_TOTAL' => 100];

        $config = [
            new DesempateConfigDTO(1, 1, 'PONTUACAO_TOTAL', null, 'DESC', null, 'Pontuação Total'),
        ];

        $resultado = $this->service->ordenar([$candidato1, $candidato2], $config);

        $this->assertSame(1, $resultado[0]->ds_posicao); // Bruno
        $this->assertSame(2, $resultado[1]->ds_posicao); // Ana
    }
}