<?php
namespace App\Services\Base;

use CodeIgniter\HTTP\Files\UploadedFile;
use Exception;

abstract class AbstractUploadService
{
    protected string $path;
    protected array $allowedExtensions;

    public function upload(?UploadedFile $file, string $filename): void
    {
        if (!$file || !$file->isValid()) {
            return;
        }

        if (!in_array($file->getExtension(), $this->allowedExtensions)) {
            throw new Exception('Tipo de arquivo não permitido');
        }

        $file->move($this->path, $filename . '.' . $file->getExtension(), true);
    }
}
