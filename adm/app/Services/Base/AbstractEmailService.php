<?php

namespace App\Services\Base;

use CodeIgniter\Email\Email;

abstract class AbstractEmailService
{
    protected Email $email;

    public function __construct()
    {
        $this->email = service('email');
    }

    protected function send(array $payload): bool
    {
        $this->email->setTo($payload['to']);
        $this->email->setSubject($payload['subject']);
        $this->email->setMessage($payload['message']);

        if (!empty($payload['cc'])) {
            $this->email->setCC($payload['cc']);
        }

        if (!empty($payload['bcc'])) {
            $this->email->setBCC($payload['bcc']);
        }

        if (!empty($payload['attachments'])) {
            foreach ($payload['attachments'] as $file) {
                $this->email->attach($file);
            }
        }

        return $this->email->send();
    }
}
