<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use Rakit\Validation\Validator;

class LoginController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function index(Request $request, Response $response, $args): Response
    {
        $view = $this->container->get('view');
        $output = $view->render('login/index', [], 'main');

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
            $errors = $validation->errors();
            // Przekierowanie z błędami walidacji
            return $response->withHeader('Location', '/zaloguj-sie')
                ->withStatus(302)
                ->withHeader('X-Errors', json_encode($errors->firstOfAll()));
        }

        // Operacje na bazie danych wewnątrz try-catch
        try {
            $db = $this->container->get('db');

            // Przygotowanie zapytania SQL
            $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'email' => $data['email'],
            ]);

            $user = $stmt->fetch();

            if (!$user) {
                // Nie znaleziono użytkownika
                return $response->withHeader('Location', '/zaloguj-sie')
                    ->withStatus(401)
                    ->withHeader('X-Error', 'Nie znaleziono użytkownika');
            }
        } catch (\PDOException $e) {
            // Logowanie błędu do loggera
            $logger = $this->container->get('logger');
            $logger->error('Błąd bazy danych', ['message' => $e->getMessage()]);

            // Obsługa błędu bazy danych
            return $response->withHeader('Location', '/zaloguj-sie')
                ->withStatus(500)
                ->withHeader('X-Error', 'Błąd podczas logowania');
        }

        // Weryfikacja hasła poza try-catch
        if (!password_verify($data['password'], $user['password'])) {
            // Błędne hasło
            return $response->withHeader('Location', '/zaloguj-sie')
                ->withStatus(401)
                ->withHeader('X-Error', 'Nieprawidłowy email lub hasło');
        }

        // Logowanie poprawne, ustawienie sesji
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];

        // Przekierowanie po zalogowaniu
        return $response->withHeader('Location', '/')
            ->withStatus(302);
    }
}
