<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class ListingController
{
    protected $container;

    // Konstruktor, który otrzymuje pojemnik DI (Dependency Injection)
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    // Wyświetlanie formularza dodawania ogłoszeń
    public function showAddListingForm(Request $request, Response $response, $args): Response
{
    $db = $this->container->get('db');

    // Pobierz wszystkie kategorie
    $query = "SELECT id, name FROM categories";
    $stmt = $db->query($query);
    $categories = $stmt->fetchAll();

    // Renderowanie widoku formularza z kategoriami
    $view = $this->container->get('view');
    $output = $view->render('listing/add_listing', ['categories' => $categories], 'main');

    $response->getBody()->write($output);
    return $response;
}


    // Obsługa przesyłania formularza
    public function submitListing(Request $request, Response $response, $args): Response
    {
        $data = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();
    
        $currentUserId = $_SESSION['user_id'] ?? null;
        if (!$currentUserId) {
            $_SESSION['negotiation_error'] = 'Musisz być zalogowany, aby dodać ogłoszenie.';
            return $response->withHeader('Location', '/login')->withStatus(302);
        }
    
        if (empty($data['job_type']) || empty($data['description']) || empty($data['payment']) || empty($data['address']) || empty($data['category_id'])) {
            $response->getBody()->write("Błąd: Wypełnij wszystkie wymagane pola.");
            return $response->withStatus(400);
        }
    
        $address = $data['address'];
        $city = null;
    
        // Wyodrębnienie miasta z adresu
        if (preg_match('/,\s?\d{2}-\d{3}\s([^\s,]+),/', $address, $matches)) {
            $city = $matches[1];
        } else {
            $city = 'Nieznane';
        }
    
        // Obsługa zdjęć
        $imageNames = [];
        if (isset($uploadedFiles['images'])) {
            foreach ($uploadedFiles['images'] as $uploadedFile) {
                if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                    $filename = $this->moveUploadedFile('resources/pictures/jobs_pictures', $uploadedFile);
                    $imageNames[] = $filename;
                }
            }
        }
    
        try {
            $db = $this->container->get('db');
    
            // Pobierz dane użytkownika z tabeli users
            $userQuery = $db->prepare('SELECT first_name, last_name, email FROM users WHERE id = :user_id');
            $userQuery->execute(['user_id' => $currentUserId]);
            $user = $userQuery->fetch();
    
            if (!$user) {
                $response->getBody()->write("Błąd: Użytkownik nie istnieje.");
                return $response->withStatus(400);
            }
    
            $employerName = $user['first_name'] . ' ' . $user['last_name'];
            $email = $user['email'];
            $phoneNumber = $data['phone_number'] ?? null;
    
            // Wstawianie ogłoszenia
            $stmt = $db->prepare('INSERT INTO listings (user_id, job_type, description, payment_type, payment, address, city, estimated_time, images, category_id, employer_name, `e-mail`, phone_number) 
                                  VALUES (:user_id, :job_type, :description, :payment_type, :payment, :address, :city, :estimated_time, :images, :category_id, :employer_name, :email, :phone_number)');
    
            $stmt->execute([
                'user_id' => $currentUserId,
                'job_type' => $data['job_type'],
                'description' => $data['description'],
                'payment_type' => $data['payment_type'],
                'payment' => $data['payment'],
                'address' => $address,
                'city' => $city,
                'estimated_time' => $data['estimated_time'] ?? null,
                'images' => json_encode($imageNames),
                'category_id' => $data['category_id'],
                'employer_name' => $employerName,
                'email' => $email,
                'phone_number' => $phoneNumber
            ]);
    
            return $response->withHeader('Location', '/?success=1')->withStatus(302);
        } catch (\PDOException $e) {
            $response->getBody()->write("Błąd podczas dodawania ogłoszenia: " . $e->getMessage());
            return $response->withStatus(500);
        }
    }
    

    // Funkcja do zapisywania plików na serwerze
    private function moveUploadedFile(string $directory, \Psr\Http\Message\UploadedFileInterface $uploadedFile): string
    {
        // Generowanie unikalnej nazwy pliku
        $basename = bin2hex(random_bytes(8));
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $filename = sprintf('%s.%s', $basename, $extension);

        // Zmiana ścieżki na public/pictures
        $fullPath = __DIR__ . '/../../public/pictures/jobs_pictures/' . $filename;

        // Tworzenie katalogów, jeśli nie istnieją
        $directoryPath = dirname($fullPath);
        if (!is_dir($directoryPath)) {
            if (!mkdir($directoryPath, 0777, true) && !is_dir($directoryPath)) {
                throw new \RuntimeException(sprintf('Nie udało się utworzyć katalogu: %s', $directoryPath));
            }
        }

        // Przenoszenie pliku do docelowej lokalizacji
        $uploadedFile->moveTo($fullPath);

        return $filename; // Zwróć nazwę pliku
    }
}
