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
        return $response->withHeader('Location', '/job/' . $listingId)->withStatus(302);
    }
}
