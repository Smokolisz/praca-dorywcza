<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use Rakit\Validation\Validator;

class LoginController
{
    protected $container;
    private $db;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->db = $this->container->get('db');

        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
    }

    public function index(Request $request, Response $response, $args): Response
    {
        $data = $request->getQueryParams();

        $isJustRegistered = isset($data['register']);

        $view = $this->container->get('view');
        $output = $view->render('login/index', [
            'isJustRegistered' => $isJustRegistered
        ], 'main');

        $response->getBody()->write($output);
        return $response;
    }

    public function login(Request $request, Response $response, $args): Response
    {
        // Pobierz dane z formularza
        $data = $request->getParsedBody();

        // Walidacja
        $validator = new Validator();
        $validation = $validator->validate($data, [
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Obsługa błędów walidacji
        if ($validation->fails()) {
            $errors = $validation->errors()->firstOfAll();
            // Przechowaj błędy w sesji
            $_SESSION['login_errors'] = $errors;
            return $response->withHeader('Location', '/zaloguj-sie')
                ->withStatus(302);
        }

        // Operacje na bazie danych wewnątrz try-catch
        try {

            // Przygotowanie zapytania SQL
            $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['email' => $data['email']]);
            $user = $stmt->fetch();

            if ($user['active'] == 0) {
                $_SESSION['login_errors'] = ['Konto nie aktywne'];
                $_SESSION['login_data'] = $data; //Zapisz dane logowania w sesji
                return $response->withHeader('Location', '/zaloguj-sie')
                    ->withStatus(302);
            }

            if ($user['verified'] == 0) {
                $_SESSION['login_errors'] = ['Email nie zweryfikowany'];
                $_SESSION['login_data'] = $data; //Zapisz dane logowania w sesji
                return $response->withHeader('Location', '/zaloguj-sie')
                    ->withStatus(302);
            }

            if (!$user) {
                $_SESSION['login_errors'] = ['Nie znaleziono użytkownika'];
                $_SESSION['login_data'] = $data; //Zapisz dane logowania w sesji
                return $response->withHeader('Location', '/zaloguj-sie')
                    ->withStatus(302);
            }
        } catch (\PDOException $e) {
            // Logowanie błędu do loggera
            $logger = $this->container->get('logger');
            $logger->error('Błąd bazy danych', ['message' => $e->getMessage()]);

            $_SESSION['login_errors'] = ['Błąd podczas logowania'];
            $_SESSION['login_data'] = $data; //Zapisz dane logowania w sesji
            return $response->withHeader('Location', '/zaloguj-sie')->withStatus(302);
        }

        // Weryfikacja hasła poza try-catch
        if (!password_verify($data['password'], $user['password'])) {
            $_SESSION['login_errors'] = ['Nieprawidłowy email lub hasło'];
            $_SESSION['login_data'] = $data; //Zapisz dane logowania w sesji
            return $response->withHeader('Location', '/zaloguj-sie')
                ->withStatus(302);
        }

        // Logowanie poprawne, ustawienie sesji
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];

        // Usuń dane logowania z sesji po poprawnym zalogowaniu
        unset($_SESSION['login_data']);

        // Przekierowanie po zalogowaniu
        return $response->withHeader('Location', '/')
            ->withStatus(302);
    }
}
