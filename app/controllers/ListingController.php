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
        // Renderowanie widoku formularza za pomocą systemu widoków
        $view = $this->container->get('view');
        $output = $view->render('listing/add_listing', [], 'main');  // Zastosuj układ 'main'

        $response->getBody()->write($output);
        return $response;
    }

    // Obsługa przesyłania formularza
    public function submitListing(Request $request, Response $response, $args): Response
    {
        // Pobieranie danych z formularza
        $data = $request->getParsedBody();

        // Pobierz aktualnie zalogowanego użytkownika
        $currentUserId = $_SESSION['user_id'] ?? null;
        if (!$currentUserId) {
            $_SESSION['negotiation_error'] = 'Musisz być zalogowany, aby dodać ogłoszenie.';
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        // Walidacja danych (przykładowa, można rozbudować)
        if (empty($data['job_type']) || empty($data['description']) || empty($data['payment']) || empty($data['address'])) {
            $response->getBody()->write("Błąd: Wypełnij wszystkie wymagane pola.");
            return $response->withStatus(400); // Zwróć błąd 400 jeśli coś jest nie tak
        }

        // Zapis do bazy danych
        try {
            $db = $this->container->get('db');  // Pobierz połączenie do bazy danych z kontenera
            $stmt = $db->prepare('INSERT INTO listings (user_id, job_type, description, payment_type, payment, address, estimated_time) 
                                  VALUES (:user_id, :job_type, :description, :payment_type, :payment, :address, :estimated_time)');
            $stmt->execute([
                'user_id' => $currentUserId,
                'job_type' => $data['job_type'],
                'description' => $data['description'],
                'payment_type' => $data['payment_type'],
                'payment' => $data['payment'],
                'address' => $data['address'],
                'estimated_time' => $data['estimated_time'] ?? null // Opcjonalnie szacowany czas pracy
            ]);

            // Przekierowanie po sukcesie
            return $response->withHeader('Location', '/')->withStatus(302); ////// przekierowanie do ogloszenia

        } catch (\PDOException $e) {
            // Obsługa błędu w przypadku problemów z bazą danych
            $response->getBody()->write("Błąd podczas dodawania ogłoszenia: " . $e->getMessage());
            return $response->withStatus(500);
        }
    }
}
