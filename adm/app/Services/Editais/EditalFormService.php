<?php
namespace App\Services\Editais;

use App\Services\Base\AbstractFormService;
use Exception;

class EditalFormService extends AbstractFormService
{
   protected function normalize(): array{
    
    $numero = preg_replace('/\D/', '', $this->request->getPost('ds_numero_edital'));
    $numero = str_pad($numero, 6, '0', STR_PAD_LEFT);

    $dataInicial = $this->request->getPost('ds_data_inicial');
    $dataFinal   = $this->request->getPost('ds_data_termino');

    if (!$dataInicial || !$dataFinal) {
        throw new Exception('Datas inválidas ou não informadas');
    }

    $parseDate = function ($date) {
        if (strpos($date, '/') !== false) {
            [$d, $m, $y] = explode('/', $date);
            return "$y-$m-$d";
        }

        if (strpos($date, '-') !== false) {
            return $date;
        }

        throw new Exception('Formato de data inválido');
    };
     
    $edital = [
        'ds_numero_edital' => $numero,
        'ds_data_inicial'  => $parseDate($dataInicial),
        'ds_data_termino'  => $parseDate($dataFinal),
        'ds_status'        => $this->request->getPost('ds_status'),
        'ds_arquivo_edital'=> $numero . '.pdf',
    ];

    $pkId = (int) $this->request->getPost('pk_id_edital');
    if ($pkId > 0) {
        $edital['pk_id_edital'] = $pkId;
    }

    return [
        'acao' => $this->request->getPost('action') ?? 'create',

        'edital' => $edital,

        'relacoes' => [
            'modo'  => $this->request->getPost('ds_modo')?? '0',
            'itens' => $this->request->getPost(
                $this->request->getPost('ds_modo') === '1'
                    ? 'ds_abrangencias'
                    : 'ds_cargos'
            ) ?? [],
        ],
    ];
}


    protected function validate(array $data): void
    {
        
        $this->require($data['edital'], 'ds_numero_edital', 'Número do edital é obrigatório');

        if ($data['edital']['ds_data_termino'] < $data['edital']['ds_data_inicial']) {
            throw new Exception('Data final deve ser maior que a inicial');
        }

        if ($data['relacoes']['modo'] === '0' && empty($data['relacoes']['itens'])) {
            throw new Exception('Selecione ao menos um cargo');
        }

        // 🔹 Unicidade ignorando o próprio registro no UPDATE
        $model = new \App\Models\EditaisModel();
        if($data['acao'] === 'create'){
            $existe = $model
                ->where('ds_numero_edital', $data['edital']['ds_numero_edital'])
                ->where('pk_id_edital !=', $data['edital']['pk_id_edital'] ?? 0)
                ->first();

            if ($existe) {
                throw new Exception('Número do edital já existe');
            }
        }
    }
}
