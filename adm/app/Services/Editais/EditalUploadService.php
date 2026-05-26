<?php
namespace App\Services\Editais;

use App\Services\Base\AbstractUploadService;

class EditalUploadService extends AbstractUploadService
{
    protected string $path;
    protected array $allowedExtensions = ['pdf'];

    public function __construct()
    {
        /**
         * ROOTPATH -> /pasta_do_sistema_Estagiarios/adm/
         * dirname(ROOTPATH) -> /pasta_do_sistema_Estagiarios/
         */
        
        $this->path = dirname(ROOTPATH)
            . DIRECTORY_SEPARATOR . 'writable'
            . DIRECTORY_SEPARATOR . 'uploads'
            . DIRECTORY_SEPARATOR . 'Editais';
            
    }
    
}

