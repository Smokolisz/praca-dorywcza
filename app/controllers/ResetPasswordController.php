<?php

namespace App\Controllers;

use App\Resources\Mails\ResetPassword\ResetPasswordMail;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use Rakit\Validation\Validator;

class ResetPasswordController
{
    protected $container;
    private $db;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->db = $container->get('db');

        if(isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
    }

    public function index(Request $request, Response $response, $args): Response
    {
        $data = [];
        if(isset($_SESSION['password-reset-email-sent']) && $_SESSION['password-reset-email-sent']) {
            $data = ['emailSent'=>1];
            unset($_SESSION['password-reset-email-sent']);
        }

        $view = $this->container->get('view');
        $output = $view->render('reset-password/index', $data, 'main');

        $response->getBody()->write($output);

        return $response;
    }

    public function sendPasswordResetEmail(Request $request, Response $response, $args): Response
    {
        // Pobierz dane z formularza
        $data = $request->getParsedBody();

        // Walidacja
        $validator = new Validator();

        $validation = $validator->validate($data, [
            'email' => 'required|min:1',
        ]);

        $validation->setMessages([
            'email:min' => 'Email musi mieć co najmniej jeden znak.',
            'required'  => 'Pole :attribute jest wymagane.',
        ]);

        $validation->validate();

        // Obsługa błędów walidacji
        if ($validation->fails()) {
            $errors = $validation->errors()->firstOfAll();

            $_SESSION['password-reset-errors'] = $errors;

            return $response->withHeader('Location', '/resetuj-haslo')
                ->withStatus(302);
        }

        $sql = <<<SQL
        SELECT * FROM users WHERE email = :email LIMIT 1
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'email' => $data['email']
        ]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        // zapisz token do resetowania hasła
        $token = uniqid();

        $sql = <<<SQL
        UPDATE users SET password_reset_token = :token WHERE email = :email LIMIT 1
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'email' => $data['email'],
            'token' => $token
        ]);

        $appConfig = $this->container->get('app-config');

        if($user) {
            $resetPasswordMail = new ResetPasswordMail($appConfig['app-url'], $token);
            $mailer = $this->container->get('mailService');
            $mailer->sendEmail($user->email, $resetPasswordMail);
        }

        unset($_SESSION['registration_errors']);
        $_SESSION['password-reset-email-sent'] = 1;
        return $response->withHeader('Location', '/resetuj-haslo')
                    ->withStatus(302);
    }

    public function edit(Request $request, Response $response, $args): Response
    {
        $token = $args['token'];

        if (!$token) {
            return $response->withHeader('Location', '/zaloguj-sie')->withStatus(302);
        }

        $sql = "SELECT * FROM users WHERE password_reset_token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);

        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$user) {
            return $response->withHeader('Location', '/zaloguj-sie')->withStatus(302);
        }
        
        $view = $this->container->get('view');
        $output = $view->render('reset-password/edit', ['token' => $token], 'main');

        $response->getBody()->write($output);

        return $response;
    }

    public function update(Request $request, Response $response, $args): Response
    {
        $token = $args['token'];

        if (!$token) {
            return $response->withHeader('Location', '/zaloguj-sie')->withStatus(302);
        }

        $sql = "SELECT * FROM users WHERE password_reset_token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$user) {
            return $response->withHeader('Location', '/zaloguj-sie')->withStatus(302);
        }

        // Pobierz dane z formularza
        $data = $request->getParsedBody();

        // Walidacja
        $validator = new Validator();

        $validation = $validator->validate($data, [
            'new_password'     => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        $validation->setMessages([
            'new_password:min'       => 'Hasło musi mieć co najmniej 6 znaków.',
            'confirm_password:same'  => 'Hasła muszą być takie same.',
            'required'               => 'Pole :attribute jest wymagane.',
        ]);

        $validation->validate();

        // Obsługa błędów walidacji
        if ($validation->fails()) {
            $errors = $validation->errors()->firstOfAll();

            // Przechowaj błędy w sesji (lub przekierowanie z błędami w nagłówkach)
            $_SESSION['reset_errors'] = $errors;

            return $response->withHeader('Location', '/resetuj-haslo/' . $token)
                ->withStatus(302);
        }
 
        $hashedPassword = password_hash($data['new_password'], PASSWORD_BCRYPT);

        // verify user
        $sql = "UPDATE users SET password = :new_password, password_reset_token=NULL WHERE id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $user->id,
            'new_password' => $hashedPassword
        ]);

        $_SESSION['password-reset-successfully'] = 1;
        return $response->withHeader('Location', '/zaloguj-sie')->withStatus(302);
    }
}
