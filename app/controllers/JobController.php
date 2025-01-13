<?php

namespace App\Controllers;

use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class JobController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function index(Request $request, Response $response, $args): Response
    {
        $db = $this->container->get('db');

        // Pobierz szczegóły ogłoszenia
        $stmt = $db->prepare("SELECT * FROM listings WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $args['id'], PDO::PARAM_INT);
        $stmt->execute();
        $job = $stmt->fetch();

        if (!$job) {
            $response->getBody()->write("Ogłoszenie nie zostało znalezione.");
            return $response->withStatus(404);
        }

        // Sprawdź, czy użytkownik jest pracodawcą
        $isEmployer = $job['user_id'] == $_SESSION['user_id'];

        // Pobierz średnią ocen i liczbę opinii dla użytkownika, który wystawił ogłoszenie
        $stmt = $db->prepare("
        SELECT COALESCE(AVG(rating), 0) AS average_rating, COUNT(*) AS review_count 
        FROM reviews 
        WHERE reviewed_user_id = :user_id
    ");
        $stmt->bindParam(':user_id', $job['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $reviewsData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Przypisz wartości do zmiennych
        $averageRating = $reviewsData['average_rating'] ?? 0; // Domyślnie 0, jeśli brak ocen
        $reviewCount = $reviewsData['review_count'] ?? 0;     // Domyślnie 0, jeśli brak opinii

        // Pobierz wszystkie kontrakty związane z ogłoszeniem
        $stmt = $db->prepare("
        SELECT c.*, u.first_name, u.last_name 
        FROM contracts c
        LEFT JOIN users u ON c.user_id = u.id
        WHERE c.job_id = :job_id AND c.status != 'rejected' AND c.status != 'accepted'  
        ");
        $stmt->bindParam(':job_id', $args['id'], PDO::PARAM_INT);
        $stmt->execute();
        $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    

        // Sprawdź, czy użytkownik już wysłał kontrakt dla tego ogłoszenia
        $stmt = $db->prepare("
        SELECT COUNT(*) AS count
        FROM contracts
        WHERE job_id = :job_id AND user_id = :user_id AND status != 'rejected'
    ");
        $stmt->bindParam(':job_id', $args['id'], PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $contractExists = $stmt->fetch()['count'] > 0;

        // Renderowanie widoku
        $view = $this->container->get('view');
        $output = $view->render('preview/index', [
            'job' => $job,
            'contracts' => $contracts,
            'isEmployer' => $isEmployer,
            'contractExists' => $contractExists,
            'averageRating' => $averageRating, // Średnia ocen
            'reviewCount' => $reviewCount,     // Liczba opinii
        ], 'main');

        $response->getBody()->write($output);
        return $response;
    }




    public function createContract(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);

        error_log('Received data: ' . print_r($data, true)); // Loguj dane wejściowe

        if (!isset($data['job_id']) || empty($_SESSION['user_id'])) {
            error_log('Invalid data or missing session.');
            $response->getBody()->write('Nieprawidłowe dane wejściowe.');
            return $response->withStatus(400);
        }

        $db = $this->container->get('db');

        // Sprawdź, czy ogłoszenie istnieje
        $stmt = $db->prepare("SELECT user_id FROM listings WHERE id = :job_id");
        $stmt->bindParam(':job_id', $data['job_id'], PDO::PARAM_INT);
        $stmt->execute();
        $employer = $stmt->fetch();
        error_log('Employer data: ' . print_r($employer, true));

        if (!$employer) {
            $response->getBody()->write('Nie znaleziono ogłoszenia.');
            return $response->withStatus(404);
        }

        // Zapisz kontrakt
        $stmt = $db->prepare("
        INSERT INTO contracts (job_id, user_id, employer_id, created_at, status) 
        VALUES (:job_id, :user_id, :employer_id, NOW(), 'pending')
    ");
        $stmt->bindParam(':job_id', $data['job_id'], PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':employer_id', $employer['user_id'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            error_log('Contract successfully created.');
            $response->getBody()->write('Kontrakt został zapisany.');
            return $response->withStatus(201);
        }

        error_log('SQL Error: ' . print_r($stmt->errorInfo(), true));
        $response->getBody()->write('Wystąpił błąd podczas zapisywania kontraktu.');
        return $response->withStatus(500);

    }





    public function acceptContract(Request $request, Response $response, $args): Response
    {
        $db = $this->container->get('db');

        // Zaktualizuj status kontraktu na 'accepted'
        $stmt = $db->prepare("UPDATE contracts SET status = 'accepted' WHERE id = :id");
        $stmt->bindParam(':id', $args['id'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $response->withHeader('Location', '/')->withStatus(302); // Przekieruj po akcji
        }

        return $response->withStatus(500)->getBody()->write('Nie udało się zaakceptować kontraktu.');
    }

    public function rejectContract(Request $request, Response $response, $args): Response
    {
        $db = $this->container->get('db');

        // Zaktualizuj status kontraktu na 'rejected'
        $stmt = $db->prepare("UPDATE contracts SET status = 'rejected' WHERE id = :id");
        $stmt->bindParam(':id', $args['id'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $response->withHeader('Location', '/')->withStatus(302); // Przekieruj po akcji
        }

        return $response->withStatus(500)->getBody()->write('Nie udało się odrzucić kontraktu.');
    }

    public function completeListing(Request $request, Response $response, $args): Response
    {
        $listingId = $args['id'] ?? null;

        if (!$listingId) {
            $_SESSION['listing_error'] = 'Brak identyfikatora ogłoszenia.';
            return $response->withHeader('Location', '/')->withStatus(400);
        }

        $currentUserId = $_SESSION['user_id'] ?? null;

        if (!$currentUserId) {
            $_SESSION['listing_error'] = 'Musisz być zalogowany, aby zakończyć ogłoszenie.';
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        $db = $this->container->get('db');

        // Pobierz ogłoszenie
        $stmt = $db->prepare("SELECT * FROM listings WHERE id = :id");
        $stmt->execute(['id' => $listingId]);
        $listing = $stmt->fetch();

        if (!$listing) {
            $_SESSION['listing_error'] = 'Ogłoszenie nie zostało znalezione.';
            return $response->withHeader('Location', '/')->withStatus(404);
        }

        // Sprawdzenie uprawnień
        if ($listing['user_id'] != $currentUserId) {
            $_SESSION['listing_error'] = 'Nie masz uprawnień do zakończenia tego ogłoszenia.';
            return $response->withHeader('Location', '/')->withStatus(403);
        }

        // Zmień status ogłoszenia na 'closed'
        $stmt = $db->prepare("UPDATE listings SET listing_status = 'closed' WHERE id = :id");
        $stmt->execute(['id' => $listingId]);

        $_SESSION['listing_success'] = 'Ogłoszenie zostało zakończone.';
        return $response->withHeader('Location', '/job/' . $listingId)->withStatus(302);
    }
}
