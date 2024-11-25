<?php

namespace App\Resources\Mails\ResetPassword;

use App\Resources\Mails\AbstractMail;

class ResetPasswordMail extends AbstractMail
{
    private string $subject = "Zmień swoje hasło - SwiftJobs";
    private string $token;
    private string $appUrl;

    public function __construct(string $appUrl, string $token)
    {
        $this->token = $token;
        $this->appUrl = $appUrl;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): string
    {
        return '
        <!DOCTYPE html>
        <html lang="pl">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Resetowanie hasła</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f7f7f7;
                    color: #333333;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                }
                .header {
                    text-align: center;
                    padding-bottom: 20px;
                }
                .header img {
                    max-width: 150px;
                }
                .content {
                    font-size: 16px;
                    line-height: 1.6;
                }
                .button {
                    display: inline-block;
                    padding: 10px 20px;
                    margin-top: 20px;
                    background-color: #4CAF50;
                    color: #ffffff;
                    text-decoration: none;
                    border-radius: 5px;
                    font-weight: bold;
                }
                .footer {
                    text-align: center;
                    font-size: 12px;
                    color: #777777;
                    margin-top: 20px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>Resetowanie hasła</h2>
                </div>
                <div class="content">
                    <p>Witaj,</p>
                    <p>Otrzymaliśmy prośbę o zresetowanie hasła do Twojego konta. Jeśli chcesz ustawić nowe hasło, kliknij poniższy przycisk:</p>
                    <p style="text-align: center;">
                        <a href="' . $this->appUrl . '/resetuj-haslo/' . $this->token . '" class="button">Zresetuj hasło</a>
                    </p>
                    <p>Jeśli nie składałeś(aś) prośby o zresetowanie hasła, zignoruj ten e-mail. Twoje hasło pozostanie bez zmian.</p>
                    <p>Z pozdrowieniami,<br>Zespół Swift-Jobs</p>
                </div>
                <div class="footer">
                    <p>© Swift-Jobs | Wszelkie prawa zastrzeżone</p>
                    <p>Nie odpowiadaj na tę wiadomość. Jeśli potrzebujesz pomocy, skontaktuj się z nami poprzez <a href="mailto:support@swift-jobs.com">support@swift-jobs.com</a>.</p>
                </div>
            </div>
        </body>
        </html>
        ';
    }
}
