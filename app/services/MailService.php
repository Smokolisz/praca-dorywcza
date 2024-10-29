<?php

namespace App\Services;

use App\Resources\Mails\AbstractMail;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    protected $mailer;

    public function __construct($host, $username, $password, $port = 587)
    {
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host = $host;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $username;
        $this->mailer->Password = $password;
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = $port;
    }

    public function sendEmail(string $to, AbstractMail $mail, ?string $from="")
    {
        try {
            $this->mailer->setFrom($from ?: "support@swift-jobs.com");
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $mail->getSubject();
            $this->mailer->Body = $mail->getBody();

            $this->mailer->send();
            return ['status' => 'success', 'message' => 'Email sent successfully'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
