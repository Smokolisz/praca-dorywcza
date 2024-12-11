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

    // Metoda wyświetlająca formularz dodawania opinii
    public function showAddReviewForm(Request $request, Response $response, $args): Response
    {
        $negotiationId = $args['negotiation_id'] ?? null;

        if (!$negotiationId) {
            $_SESSION['review_error'] = 'Brak identyfikatora negocjacji.';
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $currentUserId = $_SESSION['user_id'] ?? null;

        if (!$negotiationId || !$currentUserId) {
            $_SESSION['review_error'] = 'Brakujące dane.';
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $db = $this->container->get('db');

        // Pobierz negocjację
        $stmt = $db->prepare("SELECT * FROM negotiations WHERE id = :id");
        $stmt->execute(['id' => $negotiationId]);
        $negotiation = $stmt->fetch();

        if (!$negotiation) {
            $_SESSION['review_error'] = 'Negocjacja nie została znaleziona.';
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        // Sprawdź, czy oferta jest zaakceptowana
        if (!isset($negotiation['status']) || $negotiation['status'] !== 'accepted') {
            $_SESSION['review_error'] = 'Nie możesz wystawić opinii przed zaakceptowaniem oferty.';
            return $response->withHeader('Location', '/negocjacje/' . $negotiationId)->withStatus(302);
        }

        // Pobierz właściciela ogłoszenia
        $stmt = $db->prepare("SELECT user_id FROM listings WHERE id = :listing_id");
        $stmt->execute(['listing_id' => $negotiation['listing_id']]);
        $listingOwnerId = $stmt->fetchColumn();

        if ($currentUserId == $listingOwnerId) {
            $reviewedUserId = $negotiation['user_id']; // Zleceniobiorca
        } elseif ($currentUserId == $negotiation['user_id']) {
            $reviewedUserId = $listingOwnerId; // Zleceniodawca
        } else {
            $_SESSION['review_error'] = 'Nie masz uprawnień do tej akcji.';
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        // Renderuj formularz opinii
        $view = $this->container->get('view');
        $output = $view->render('reviews/add_review', [
            'negotiationId' => $negotiationId,
            'reviewedUserId' => $reviewedUserId,
        ], 'main');
        $response->getBody()->write($output);
        return $response;
    }


    // Metoda obsługująca dodawanie opinii z zapisem zdjęć
    public function submitReview(Request $request, Response $response, $args): Response
    {
        $data = $request->getParsedBody();
        $files = $request->getUploadedFiles();

        // Upewniamy się, że 'photos' jest zawsze tablicą
        $photos = $files['photos'] ?? [];
        if (!is_array($photos)) {
            $photos = [$photos];
        }

        // Teraz $photos powinno być tablicą wszystkich plików
        // Możesz tymczasowo sprawdzić co jest w $photos:
        // var_dump($photos); die(); 
        // aby mieć pewność, że masz wiele plików.

        $negotiationId = $data['negotiation_id'] ?? null;
        $reviewedUserId = $data['reviewed_user_id'] ?? null;
        $rating = $data['rating'] ?? null;
        $pros = $data['pros'] ?? null;
        $cons = $data['cons'] ?? null;
        $comment = $data['comment'] ?? null;
        $currentUserId = $_SESSION['user_id'] ?? null;

        if (!$negotiationId || !$reviewedUserId || !$rating || !$currentUserId) {
            $_SESSION['review_error'] = 'Brakujące dane.';
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        if ($rating < 1 || $rating > 5) {
            $_SESSION['review_error'] = 'Ocena musi być w zakresie od 1 do 5.';
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $photoPaths = $this->savePhotos($photos);
        $photosJson = json_encode($photoPaths);

        $db = $this->container->get('db');

        $stmt = $db->prepare("
            SELECT * FROM reviews 
            WHERE negotiation_id = :negotiation_id AND reviewer_id = :reviewer_id
        ");
        $stmt->execute([
            'negotiation_id' => $negotiationId,
            'reviewer_id' => $currentUserId
        ]);
        $existingReview = $stmt->fetch();

        if ($existingReview) {
            $_SESSION['review_error'] = 'Już wystawiłeś opinię dla tej negocjacji.';
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $stmt = $db->prepare("SELECT * FROM negotiations WHERE id = :id");
        $stmt->execute(['id' => $negotiationId]);
        $negotiation = $stmt->fetch();

        if (!$negotiation) {
            $_SESSION['review_error'] = 'Negocjacja nie została znaleziona.';
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $stmt = $db->prepare("
            INSERT INTO reviews (reviewer_id, reviewed_user_id, listing_id, rating, comment, negotiation_id, pros, cons, photos)
            VALUES (:reviewer_id, :reviewed_user_id, :listing_id, :rating, :comment, :negotiation_id, :pros, :cons, :photos)
        ");
        $stmt->execute([
            'reviewer_id' => $currentUserId,
            'reviewed_user_id' => $reviewedUserId,
            'listing_id' => $negotiation['listing_id'],
            'rating' => $rating,
            'comment' => $comment,
            'negotiation_id' => $negotiationId,
            'pros' => $pros,
            'cons' => $cons,
            'photos' => $photosJson
        ]);

        $_SESSION['review_success'] = 'Twoja opinia została dodana pomyślnie!';
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    // Funkcja do zapisu przesłanych plików
    private function savePhotos(array $photos)
    {
        $paths = [];
        $uploadDir = __DIR__ . '/../../public/review_photos';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($photos as $photo) {
            if ($photo->getError() === UPLOAD_ERR_OK) {
                $originalFileName = $photo->getClientFilename();
                $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
                $newFileName = 'review_' . time() . '_' . uniqid() . '.' . $extension;
                $photo->moveTo($uploadDir . '/' . $newFileName);
                $paths[] = $newFileName;
            }
        }

        return $paths;
    }

    // Metoda wyświetlająca wszystkie opinie (przykładowa)
    public function showReviews(Request $request, Response $response, $args): Response
    {
        try {
            $db = $this->container->get('db');

            // Pobierz wszystkie opinie
            $stmt = $db->prepare("
                SELECT r.*, 
                       CONCAT(u.first_name, ' ', u.last_name) AS reviewer_name,
                       l.description AS listing_description
                FROM reviews r
                JOIN users u ON r.reviewer_id = u.id
                JOIN listings l ON r.listing_id = l.id
                ORDER BY r.created_at DESC
            ");
            $stmt->execute();
            $reviews = $stmt->fetchAll();

            // Renderuj widok z opiniami
            $view = $this->container->get('view');
            return $view->render($response, 'reviews/view_reviews', [
                'reviews' => $reviews,
            ]);
        } catch (\Exception $e) {
            error_log("Error fetching reviews: " . $e->getMessage());
            $_SESSION['review_error'] = 'Wystąpił błąd podczas ładowania opinii.';
            return $response->withHeader('Location', '/')->withStatus(302);
        }
    }
}
