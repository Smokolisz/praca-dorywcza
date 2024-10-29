<?php

namespace App\Controllers;

use App\Resources\Mails\Register\ConfirmEmail;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use Rakit\Validation\Validator;

class RegisterController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function index(Request $request, Response $response, $args): Response
    {
        $view = $this->container->get('view');
        $output = $view->render('register/index', [], 'main');

        $response->getBody()->write($output);
        return $response;
    }

    public function store(Request $request, Response $response, $args): Response
    {
        // Pobierz dane z formularza
        $data = $request->getParsedBody();

        // Walidacja
        $validator = new Validator();
        $validation = $validator->validate($data, [
            'first-name'       => 'required',
            'last-name'        => 'required',
            'email'            => 'required|email',
            'password'         => 'required|min:6',
            'confirm-password' => 'required|same:password',
            'terms'            => 'required|accepted'
        ]);

        // Obsługa błędów walidacji
        if ($validation->fails()) {
            $errors = $validation->errors();
            // Przekierowanie z błędami
            return $response->withHeader('Location', '/zarejestruj-sie')
                ->withStatus(302)
                ->withHeader('X-Errors', json_encode($errors->firstOfAll()));
        }

        // Dostęp do bazy danych
        $db = $this->container->get('db');

        // Przygotowanie zapytania SQL
        $sql = <<<SQL
        INSERT INTO users (email, password, first_name, last_name, active, role, token) 
        VALUES (:email, :password, :first_name, :last_name, :active, :role, :token)
        SQL;

        // Haszowanie hasła
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        try {

            $token = uniqid();

            $stmt = $db->prepare($sql);
            $result = $stmt->execute([
                'email' => $data['email'],
                'password' => $hashedPassword,
                'first_name' => $data['first-name'],
                'last_name' => $data['last-name'],
                'active' => 0,
                'role' => 'user',
                'token' => $token,
            ]);

            if ($result) {
                // Przekierowanie po udanej rejestracji

                $confirmEmail = new ConfirmEmail($token);

                $mailer = $this->container->get('mailService');
                $mailer->sendEmail($data['email'], $confirmEmail);

                return $response->withHeader('Location', '/konto')
                    ->withStatus(302);
            }
        } catch (\PDOException $e) {
            // Logowanie błędu
            $logger = $this->container->get('logger');
            $logger->error('Błąd bazy danych', ['message' => $e->__toString()]);

            // Przekierowanie w przypadku błędu
            return $response->withHeader('Location', '/zarejestruj-sie')
                ->withStatus(500)
                ->withHeader('X-Error', 'Błąd podczas rejestracji');
        }
    }
}
