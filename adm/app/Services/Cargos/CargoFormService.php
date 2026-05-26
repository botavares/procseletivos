<?php
namespace App\Services\Cargos;
use App\Services\Base\AbstractFormService;
use Exception;

class CargoFormService extends AbstractFormService{
    protected function normalize(): array
{
    return [
        'acao' => $this->request->getPost('action') ?? 'create',
        'Cargo' => [
            'pk_id_Cargo'       => $this->request->getPost('pk_id_Cargo'),
            'ds_nome_Cargo'     => $this->request->getPost('ds_nome_Cargo'),
            'fk_id_abrangencia' => $this->request->getPost('fk_id_abrangencia'),
        ],
    ];
}

    public function validate(array $data): void{
        $this->require($data['Cargo'], 'ds_nome_Cargo', 'O nome do Cargo deve ser informado.');
        $this->require($data['Cargo'], 'fk_id_abrangencia', 'A abrangência do Cargo deve ser informada.');
    }

}
