<?php

namespace App\Resources\Mails\Register;

use App\Resources\Mails\AbstractMail;

class ConfirmEmail extends AbstractMail
{
    private string $subject = "Potwierdź adres e-mail";
    private string $token;
    private string $appUrl;

    public function __construct(string $appUrl, string $token)
    {
        $this->token = $token;
        $this->appUrl = $appUrl;
    }

    public function getSubject() : string
    {
        return $this->subject;
    }

    public function getBody() : string
    {
        return '
        <!DOCTYPE html>
        <html lang="pl">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Potwierdzenie adresu email</title>
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
                    <h2>Potwierdzenie adresu email</h2>
                </div>
                <div class="content">
                    <p>Witaj,</p>
                    <p>Dziękujemy za rejestrację w naszym serwisie. Aby zakończyć proces rejestracji, prosimy o potwierdzenie swojego adresu e-mail, klikając poniższy przycisk:</p>
                    <p style="text-align: center;">
                        <a href="'.$this->appUrl.'/potwierdz-email/'.$this->token.'" class="button">Potwierdź adres email</a>
                    </p>
                    <p>Jeśli nie zakładałeś(aś) konta, zignoruj ten e-mail.</p>
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
