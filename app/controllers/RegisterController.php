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

        // Definiowanie niestandardowych wiadomości w języku polskim
        $validation->setMessages([
            'email:email'            => 'Podany adres email jest niepoprawny.',
            'confirm-password:same'  => 'Hasło musi być takie samo jak hasło główne.',
            'password:min'           => 'Hasło musi mieć co najmniej 6 znaków.',
            'terms:accepted'         => 'Musisz zaakceptować regulamin.',
            'required'               => 'Pole :attribute jest wymagane.',
        ]);

        $validation->validate();

        // Obsługa błędów walidacji
        if ($validation->fails()) {
            $errors = $validation->errors()->firstOfAll();

            // Przechowaj błędy w sesji (lub przekierowanie z błędami w nagłówkach)
            $_SESSION['registration_errors'] = $errors;
            $_SESSION['register_data'] = $data;

            return $response->withHeader('Location', '/zarejestruj-sie')
                ->withStatus(302);
        }

        // Dostęp do bazy danych
        $db = $this->container->get('db');

        // Sprawdzenie, czy email już istnieje w bazie
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute(['email' => $data['email']]);
        $emailExists = $stmt->fetchColumn();

        if ($emailExists) {
            // Jeśli email już istnieje, zapisujemy błąd w sesji
            $_SESSION['registration_errors'] = ['email' => 'Podany adres email jest już zarejestrowany.'];
            $_SESSION['register_data'] = $data;
            return $response->withHeader('Location', '/zarejestruj-sie')->withStatus(302);
        }

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
                unset($_SESSION['register_data']); // Jeśli rejestracja przebiegnie pomyślnie, usuń dane z sesji
                return $response->withHeader('Location', '/zaloguj-sie')
                    ->withStatus(302);
            }
        } catch (\PDOException $e) {
            // Logowanie błędu
            $logger = $this->container->get('logger');
            $logger->error('Błąd bazy danych', ['message' => $e->__toString()]);
            $_SESSION['register_data'] = $data;
            // Przekierowanie w przypadku błędu
            return $response->withHeader('Location', '/zarejestruj-sie')
                ->withStatus(500)
                ->withHeader('X-Error', 'Błąd podczas rejestracji');
        }
    }
}
