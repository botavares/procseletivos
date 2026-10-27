<?php

declare(strict_types=1);

namespace Tests\Unit\DTOs\Admin;

use App\DTOs\Admin\ProcessosSeletivosDTO;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ProcessosSeletivosDTOTest extends CIUnitTestCase
{
    public function testConstrutor(): void
    {
        $dto = new ProcessosSeletivosDTO(
            idProcesso: 1,
            idSecretaria: 2,
            numeroProcesso: 12345,
            anoProcesso: 2026,
            objeto: 'Objeto Teste',
            situacao: 1,
            inicioInscricao: '2026-01-01',
            fimInscricao: '2026-02-01',
            divulgaResultado: '2026-03-01',
            mesesValidade: 12,
            prorrogado: 0,
        );

        $this->assertSame(1, $dto->idProcesso);
        $this->assertSame(2, $dto->idSecretaria);
        $this->assertSame(12345, $dto->numeroProcesso);
        $this->assertSame(2026, $dto->anoProcesso);
        $this->assertSame('Objeto Teste', $dto->objeto);
        $this->assertSame(1, $dto->situacao);
        $this->assertSame('2026-01-01', $dto->inicioInscricao);
        $this->assertSame('2026-02-01', $dto->fimInscricao);
        $this->assertSame('2026-03-01', $dto->divulgaResultado);
        $this->assertSame(12, $dto->mesesValidade);
        $this->assertSame(0, $dto->prorrogado);
    }

    public function testFromArray(): void
    {
        $data = [
            'pk_id_processo'        => 10,
            'fk_id_secretaria'      => 3,
            'nm_numero_processo'    => 99999,
            'nm_ano_processo'     => 2025,
            'ds_objeto'           => 'Objeto Array',
            'nm_situacao'         => 2,
            'dt_inicio_inscricao' => '2025-06-01',
            'dt_fim_inscricao'    => '2025-07-01',
            'dt_divulga_resultado' => '2025-08-01',
            'nm_meses_validade'   => 6,
            'nm_prorrogado'       => 1,
        ];

        $dto = ProcessosSeletivosDTO::fromArray($data);

        $this->assertSame(10, $dto->idProcesso);
        $this->assertSame(3, $dto->idSecretaria);
        $this->assertSame(99999, $dto->numeroProcesso);
        $this->assertSame(2, $dto->situacao);
        $this->assertSame(1, $dto->prorrogado);
        $this->assertSame('Objeto Array', $dto->objeto);
    }

    public function testToArraySemId(): void
    {
        $dto = new ProcessosSeletivosDTO(
            idProcesso: null,
            idSecretaria: 1,
            numeroProcesso: 1,
            anoProcesso: 2026,
            objeto: 'Teste',
            situacao: 1,
            inicioInscricao: '2026-01-01',
            fimInscricao: '2026-02-01',
            divulgaResultado: null,
            mesesValidade: 12,
            prorrogado: 0,
        );

        $array = $dto->toArray();
        $this->assertArrayNotHasKey('pk_id_processo', $array);
        $this->assertSame('Teste', $array['ds_objeto']);
        $this->assertSame(1, $array['nm_numero_processo']);
    }

    public function testToArrayComId(): void
    {
        $dto = new ProcessosSeletivosDTO(
            idProcesso: 5,
            idSecretaria: 1,
            numeroProcesso: 1,
            anoProcesso: 2026,
            objeto: 'Teste',
            situacao: 1,
            inicioInscricao: '2026-01-01',
            fimInscricao: '2026-02-01',
            divulgaResultado: null,
            mesesValidade: 12,
            prorrogado: 0,
        );

        $array = $dto->toArray();
        $this->assertArrayHasKey('pk_id_processo', $array);
        $this->assertSame(5, $array['pk_id_processo']);
    }
}
