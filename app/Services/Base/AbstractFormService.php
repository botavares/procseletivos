<?php
namespace App\Services\Base;

use CodeIgniter\HTTP\IncomingRequest;
use Exception;

abstract class AbstractFormService
{
    protected IncomingRequest $request;

    public function __construct(IncomingRequest $request)
    {
        $this->request = $request;
    }

    final public function handle(): array
    {
        $data = $this->normalize();
        $this->validate($data);

        return $data;
    }

    abstract protected function normalize(): array;
    abstract protected function validate(array $data): void;

    protected function require(array $data, string $key, string $message): void
    {
        if (!isset($data[$key]) || $data[$key] === '' || $data[$key] === null) {
            throw new Exception($message);
        }
    }
}
