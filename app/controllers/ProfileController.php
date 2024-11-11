<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class ProfileController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    // Wyświetlanie profilu użytkownika
    public function index(Request $request, Response $response, $args): Response
    {
        $db = $this->container->get("db");
        $view = $this->container->get("view");

        $userId = $_SESSION['user_id'] ?? null;

        if ($userId) {
            $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            $userData = $stmt->fetch();

            if (!$userData) {
                return $response->withHeader('Location', '/zaloguj-sie')->withStatus(302);
            }

            // Renderowanie widoku profilu
            $output = $view->render('account/profile', ['user' => $userData], "main");
            $response->getBody()->write($output);
            return $response;
        }

        return $response->withHeader('Location', '/zaloguj-sie')->withStatus(302);
    }

    // Wyświetlanie formularza edycji profilu
    public function edit(Request $request, Response $response, $args): Response
    {
        $db = $this->container->get("db");
        $view = $this->container->get("view");

        $userId = $_SESSION['user_id'] ?? null;

        if ($userId) {
            $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            $userData = $stmt->fetch();

            // Renderowanie widoku edycji profilu
            $output = $view->render('account/edit', ['user' => $userData], "main");
            $response->getBody()->write($output);
            return $response;
        }

        return $response->withHeader('Location', '/zaloguj-sie')->withStatus(302);
    }

    // Zapisanie zmian w profilu użytkownika
    public function updateProfile(Request $request, Response $response, $args): Response
    {
        $db = $this->container->get("db");
        $userId = $_SESSION['user_id'] ?? null;

        if ($userId) {
            $parsedBody = $request->getParsedBody();
            $firstName = $parsedBody['first_name'] ?? '';
            $lastName = $parsedBody['last_name'] ?? '';

            $stmt = $db->prepare("UPDATE users SET first_name = :first_name, last_name = :last_name WHERE id = :id");
            $stmt->bindParam(':first_name', $firstName);
            $stmt->bindParam(':last_name', $lastName);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();

            return $response->withHeader('Location', '/profil')->withStatus(302);
        }

        return $response->withHeader('Location', '/zaloguj-sie')->withStatus(302);
    }

    // Wyświetlanie formularza zmiany hasła
    public function updatePasswordForm(Request $request, Response $response, $args): Response
    {
        $view = $this->container->get("view");
        $output = $view->render('account/change_password', [], "main");
        $response->getBody()->write($output);
        return $response;
    }

    // Zapisanie nowego hasła
    public function updatePassword(Request $request, Response $response, $args): Response
    {
        $db = $this->container->get("db");
        $view = $this->container->get("view");
        $userId = $_SESSION['user_id'] ?? null;

        if ($userId) {
            $parsedBody = $request->getParsedBody();
            $currentPassword = $parsedBody['current_password'] ?? '';
            $newPassword = $parsedBody['new_password'] ?? '';

            // Pobierz obecne hasło z bazy
            $stmt = $db->prepare("SELECT password FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            $user = $stmt->fetch();

            if ($user && password_verify($currentPassword, $user['password'])) {
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                $updateStmt = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
                $updateStmt->bindParam(':password', $hashedPassword);
                $updateStmt->bindParam(':id', $userId);
                $updateStmt->execute();

                // Ponowne pobranie danych użytkownika po zmianie hasła
                $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
                $stmt->bindParam(':id', $userId);
                $stmt->execute();
                $userData = $stmt->fetch();

                // Renderowanie widoku profilu po zmianie hasła
                $output = $view->render('account/profile', ['user' => $userData], "main");
                $response->getBody()->write($output);
                return $response;
            }

            // W przypadku błędu renderujemy widok zmiany hasła ponownie
            $output = $view->render('account/change_password', ['error' => 'Niepoprawne obecne hasło'], "main");
            $response->getBody()->write($output);
            return $response;
        }

        return $response->withHeader('Location', '/zaloguj-sie')->withStatus(302);
    }



    // Wyświetlanie powiadomień użytkownika
    public function notifications(Request $request, Response $response, $args): Response
    {
        $db = $this->container->get("db");
        $view = $this->container->get("view");

        $userId = $_SESSION['user_id'] ?? null;

        if ($userId) {
            $stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $notifications = $stmt->fetchAll();

            $output = $view->render('account/notifications', ['notifications' => $notifications], "main");
            $response->getBody()->write($output);
            return $response;
        }

        return $response->withHeader('Location', '/zaloguj-sie')->withStatus(302);
    }

    // Wyświetlanie historii aktywności użytkownika
    public function activityLog(Request $request, Response $response, $args): Response
    {
        $db = $this->container->get("db");
        $view = $this->container->get("view");

        $userId = $_SESSION['user_id'] ?? null;

        if ($userId) {
            $stmt = $db->prepare("
                SELECT first_name, last_name, date_created, last_login_date, updated_at, active, date_removed 
                FROM users 
                WHERE id = :id
            ");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            $userActivity = $stmt->fetch();

            $output = $view->render('account/activity_log', ['activity' => $userActivity], "main");
            $response->getBody()->write($output);

            return $response;
        }

        return $response->withHeader('Location', '/zaloguj-sie')->withStatus(302);
    }



    // Przesyłanie zdjęcia profilowego
    public function uploadProfilePicture(Request $request, Response $response, $args): Response
    {
        $db = $this->container->get("db");
        $userId = $_SESSION['user_id'] ?? null;

        if ($userId && $request->getUploadedFiles()) {
            $uploadedFile = $request->getUploadedFiles()['profile_picture'];

            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $directory = __DIR__ . '/../../public/profile_pictures';
                $filename = sprintf('%s.%s', $userId, pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION));
                $uploadedFile->moveTo($directory . '/' . $filename);

                $updateStmt = $db->prepare("UPDATE users SET profile_picture = :profile_picture WHERE id = :id");
                $updateStmt->bindParam(':profile_picture', $filename);
                $updateStmt->bindParam(':id', $userId);
                $updateStmt->execute();

                return $response->withHeader('Location', '/profil')->withStatus(302);
            }
        }

        return $response->withHeader('Location', '/profil')->withStatus(302);
    }
}
