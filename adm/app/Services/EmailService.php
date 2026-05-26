<?php

namespace App\Services;

use App\Services\Base\AbstractEmailService;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService extends AbstractEmailService
{
    public function enviar(array $dados): bool
    {
        require_once ROOTPATH . 'vendor/autoload.php';

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp-relay.brevo.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = '9f05aa001@smtp-brevo.com';
            $mail->Password   = 'xsmtpsib-6f618d2cb0e870067cc7c47669cdfe9600f2bb1f1db1ed7bb8254b95ec8cfeb4-HZ9as3kEQgyH4QBQ';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom(
                'pmdivinopolis.contatos@gmail.com',
                'Prefeitura Municipal de Divinópolis'
            );

            $mail->addAddress($dados['email'], $dados['nome']);

            $mail->CharSet = 'UTF-8';
            $mail->isHTML(true);
            $mail->Subject = $dados['assunto'];
            $mail->Body    = $dados['mensagem'];

            return $mail->send();

        } catch (Exception $e) {
            log_message('error', 'Erro ao enviar email: ' . $mail->ErrorInfo);
            return false;
        }
    }
}
