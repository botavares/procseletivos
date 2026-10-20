<?php

declare(strict_types=1);

namespace App\Exceptions\Domain;

use Exception;

class EditalForaDoPrazoException extends Exception
{
    public function __construct(string $message = 'O edital está fora do período de inscrição.')
    {
        parent::__construct($message);
    }
}
