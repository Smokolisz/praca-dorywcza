<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class ReviewController
{
    protected $container;

    // Konstruktor
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    // Wyświetlenie formularza dodawania opinii
    public function showAddReviewForm(Request $request, Response $response, $args): Response
    {
        $listingId = $args['listing_id'] ?? null;
        $currentUserId = $_SESSION['user_id'] ?? null;

        if (!$currentUserId) {
            $_SESSION['review_error'] = 'Musisz być zalogowany, aby dodać opinię.';
            return $response->withHeader('Location', '/zaloguj-sie')->withStatus(302);
        }

        if (!$listingId) {
            $_SESSION['review_error'] = 'Brak identyfikatora ogłoszenia.';
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $db = $this->container->get('db');

        // Sprawdź, czy ogłoszenie istnieje
        $stmt = $db->prepare("SELECT user_id FROM listings WHERE id = :id AND listing_status = 'closed'");
        $stmt->execute(['id' => $listingId]);
        $listingOwnerId = $stmt->fetchColumn();

        if (!$listingOwnerId) {
            $_SESSION['review_error'] = 'Nie można wystawić opinii dla tego ogłoszenia.';
            return $response->withHeader('Location', '/')->withStatus(403);
        }



        $view = $this->container->get('view');
        $output = $view->render('reviews/add_review', [
            'listingId' => $listingId,
            'reviewedUserId' => $listingOwnerId,
        ], 'main');

        $response->getBody()->write($output);
        return $response;
    }

    // Obsługa dodawania opinii
    public function submitReview(Request $request, Response $response, $args): Response
    {
        $data = $request->getParsedBody();

        $listingId = $data['listing_id'] ?? null;
        $reviewedUserId = $data['reviewed_user_id'] ?? null;
        $rating = $data['rating'] ?? null;
        $pros = $data['pros'] ?? null;
        $cons = $data['cons'] ?? null;
        $comment = $data['comment'] ?? null;
        $currentUserId = $_SESSION['user_id'] ?? null;

        if (!$listingId || !$reviewedUserId || !$rating || !$currentUserId) {
            $_SESSION['review_error'] = 'Brakujące dane.';
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        if ($rating < 1 || $rating > 5) {
            $_SESSION['review_error'] = 'Ocena musi być w zakresie od 1 do 5.';
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $db = $this->container->get('db');

        // Sprawdź, czy opinia już istnieje
        $stmt = $db->prepare("
            SELECT * FROM reviews 
            WHERE reviewer_id = :reviewer_id AND listing_id = :listing_id
        ");
        $stmt->execute([
            'reviewer_id' => $currentUserId,
            'listing_id' => $listingId,
        ]);
        if ($stmt->fetch()) {
            $_SESSION['review_error'] = 'Już wystawiłeś opinię dla tego ogłoszenia.';
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        // Dodaj opinię
        $stmt = $db->prepare("
            INSERT INTO reviews (reviewer_id, reviewed_user_id, listing_id, rating, comment, pros, cons, created_at)
            VALUES (:reviewer_id, :reviewed_user_id, :listing_id, :rating, :comment, :pros, :cons, NOW())
        ");
        $stmt->execute([
            'reviewer_id' => $currentUserId,
            'reviewed_user_id' => $reviewedUserId,
            'listing_id' => $listingId,
            'rating' => $rating,
            'comment' => $comment,
            'pros' => $pros,
            'cons' => $cons,
        ]);

        $_SESSION['review_success'] = 'Twoja opinia została dodana pomyślnie!';

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    

    // Metoda wyświetlająca wszystkie opinie (przykładowa)
    public function showReviews(Request $request, Response $response, $args): Response
{
    try {
        $db = $this->container->get('db');
        $reviewedUserId = $args['user_id'] ?? null;

        // Pobierz informacje o użytkowniku
        $stmtUser = $db->prepare("SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM users WHERE id = :user_id");
        $stmtUser->execute(['user_id' => $reviewedUserId]);
        $user = $stmtUser->fetch();

        if (!$user) {
            throw new \Exception("Nie znaleziono użytkownika.");
        }

        // Pobierz opinie
        $stmt = $db->prepare("SELECT * FROM reviews WHERE reviewed_user_id = :user_id");
        $stmt->execute(['user_id' => $reviewedUserId]);
        $reviews = $stmt->fetchAll();

        // Dane dla widoku
        $userName = $user['full_name'];
        $isOwnProfile = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $reviewedUserId;

        // Ścieżka do pliku widoku
        require_once __DIR__ . '/../resources/views/reviews/view_user_reviews.php';





        if (!file_exists($viewPath)) {
            throw new \Exception("Plik widoku nie istnieje: " . $viewPath);
        }

        // Załaduj widok
        ob_start();
        include $viewPath;
        $output = ob_get_clean();

        $response->getBody()->write($output);
        return $response;
    } catch (\Exception $e) {
        error_log("Błąd w showReviews: " . $e->getMessage());
        $response->getBody()->write("Błąd: " . $e->getMessage());
        return $response->withStatus(500);
    }
}

public function showUserReviews(Request $request, Response $response, $args): Response
{
    try {
        $db = $this->container->get('db');
        $userId = $args['user_id'] ?? null;

        if (!$userId) {
            throw new \Exception("Brak ID użytkownika.");
        }

        // Pobierz nazwę użytkownika
        $stmt = $db->prepare("SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM users WHERE id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $user = $stmt->fetch();

        if (!$user) {
            throw new \Exception("Nie znaleziono użytkownika o ID: " . $userId);
        }

        // Pobierz opinie o użytkowniku
        $stmt = $db->prepare("
            SELECT r.rating, r.comment, r.created_at, CONCAT(u.first_name, ' ', u.last_name) AS reviewer_name,
            r.pros, r.cons
            FROM reviews r
            JOIN users u ON r.reviewer_id = u.id
            WHERE r.reviewed_user_id = :user_id
            ORDER BY r.created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        $reviews = $stmt->fetchAll();

        $userName = $user['full_name'];

        
        $view = $this->container->get('view');
        $output = $view->render('reviews/view_user_reviews', [
            "reviews"=>$reviews,
            "isOwnProfile"=>false,
            "userName"=>$userName
        ], 'main');
        $response->getBody()->write($output);
        return $response;

    } catch (\Exception $e) {
        error_log("Błąd w showUserReviews: " . $e->getMessage());
        $response->getBody()->write("Błąd: " . $e->getMessage());
        return $response->withStatus(500);

    }
}


}