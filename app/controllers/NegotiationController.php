<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class NegotiationController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    // Wyświetlanie negocjacji i historii
    public function showNegotiation(Request $request, Response $response, $args): Response
    {
        $db = $this->container->get('db');
        $view = $this->container->get('view');
        $negotiationId = $args['id'] ?? null;

        if (!$negotiationId) {
            $_SESSION['negotiation_error'] = 'Identyfikator negocjacji jest wymagany.';
            return $response->withHeader('Location', '/')->withStatus(400);
        }

        // Pobranie danych negocjacji
        $stmt = $db->prepare("SELECT * FROM negotiations WHERE id = :id");
        $stmt->execute(['id' => $negotiationId]);
        $negotiation = $stmt->fetch();

        if (!$negotiation) {
            $_SESSION['negotiation_error'] = 'Negocjacja nie została znaleziona.';
            return $response->withHeader('Location', '/')->withStatus(404);
        }

        // Pobranie danych ogłoszenia
        $listingId = $negotiation['listing_id'];
        $stmt = $db->prepare("SELECT * FROM listings WHERE id = :id");
        $stmt->execute(['id' => $listingId]);
        $listing = $stmt->fetch();

        // Pobranie historii negocjacji
        $stmt = $db->prepare("
            SELECT nh.*, CONCAT(u.first_name, ' ', u.last_name) AS user_name
            FROM negotiation_history nh
            JOIN users u ON nh.user_id = u.id
            WHERE negotiation_id = :id
            ORDER BY nh.created_at DESC
        ");
        $stmt->execute(['id' => $negotiationId]);
        $history = $stmt->fetchAll();

        // Renderowanie widoku szczegółów negocjacji
        $output = $view->render('negotiation/index', [
            'negotiationId' => $negotiationId,
            'negotiation' => $negotiation,
            'listing' => $listing,
            'history' => $history
        ], 'main');
        $response->getBody()->write($output);
        return $response;
    }

    // Rozpoczęcie nowej negocjacji
    public function startNegotiation(Request $request, Response $response, $args): Response
    {
        $data = $request->getParsedBody();
        $offerAmount = $data['offer_amount'] ?? null;
        $justification = $data['justification'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;
        $listingId = $data['listing_id'] ?? null;

        if (!$listingId || !$offerAmount || !$userId) {
            $_SESSION['negotiation_error'] = 'Brakujące dane do rozpoczęcia negocjacji.';
            return $response->withHeader('Location', '/')->withStatus(400);
        }

        try {
            $db = $this->container->get('db');

            // Sprawdź, czy ogłoszenie istnieje
            $stmt = $db->prepare("SELECT * FROM listings WHERE id = :id");
            $stmt->execute(['id' => $listingId]);
            $listing = $stmt->fetch();

            if (!$listing) {
                $_SESSION['negotiation_error'] = 'Ogłoszenie nie zostało znalezione.';
                return $response->withHeader('Location', '/')->withStatus(404);
            }
            //Sprawdź czy ogłoszenie jest otwarte
            if ($listing['status'] !== 'open') {
                $_SESSION['negotiation_error'] = 'To ogłoszenie jest zamknięte i nie przyjmuje nowych negocjacji.';
                return $response->withHeader('Location', '/negocjacje/start/' . $listingId)->withStatus(403);
            }

            // Utwórz nową negocjację
            $stmt = $db->prepare("
            INSERT INTO negotiations (listing_id, user_id, offer_amount, justification, status, created_at, updated_at) 
            VALUES (:listing_id, :user_id, :offer_amount, :justification, 'pending', NOW(), NOW())
            ");
            $stmt->execute([
                'listing_id' => $listingId,
                'user_id' => $userId,
                'offer_amount' => $offerAmount,
                'justification' => $justification,
            ]);

            $_SESSION['negotiation_success'] = 'Twoja oferta została złożona pomyślnie!';
            return $response->withHeader('Location', '/negocjacje/start/' . $listingId)->withStatus(302);
        } catch (\PDOException $e) {
            $response->getBody()->write("Błąd podczas zapisu do bazy danych: " . $e->getMessage());
            return $response->withStatus(500);
        }
    }




    // Formularz rozpoczęcia nowej negocjacji
    public function startNegotiationForm(Request $request, Response $response, $args): Response
    {
        $db = $this->container->get('db');
        $view = $this->container->get('view');
        $listingId = $args['id'] ?? null;

        if (!$listingId) {
            $_SESSION['negotiation_error'] = 'Identyfikator ogłoszenia jest wymagany.';
            return $response->withHeader('Location', '/')->withStatus(400);
        }

        // Pobranie danych ogłoszenia
        $stmt = $db->prepare("SELECT * FROM listings WHERE id = :id");
        $stmt->execute(['id' => $listingId]);
        $listing = $stmt->fetch();

        if (!$listing) {
            $_SESSION['negotiation_error'] = 'Ogłoszenie nie zostało znalezione.';
            return $response->withHeader('Location', '/')->withStatus(404);
        }

        // Pobierz identyfikator właściciela ogłoszenia
        $listingOwnerId = $listing['user_id'];
        $currentUserId = $_SESSION['user_id'] ?? null;

        // Pobranie statusu ogłoszenia
        $listingStatus = $listing['status'];

        // Pobranie negocjacji dla tego ogłoszenia
        $stmt = $db->prepare("
        SELECT n.*, CONCAT(u.first_name, ' ', u.last_name) AS user_name
        FROM negotiations n
        JOIN users u ON n.user_id = u.id
        WHERE n.listing_id = :listing_id
        ORDER BY n.created_at DESC
        ");
        $stmt->execute(['listing_id' => $listingId]);
        $negotiations = $stmt->fetchAll();

        $output = $view->render('negotiation/start', [
            'listingId' => $listingId,
            'listing' => $listing,
            'negotiations' => $negotiations,
            'listingOwnerId' => $listingOwnerId,
            'currentUserId' => $currentUserId,
            'listingStatus' => $listingStatus
        ], 'main');
        $response->getBody()->write($output);
        return $response;
    }



    // Akceptacja oferty
    public function acceptOffer(Request $request, Response $response, $args): Response
    {
        $negotiationId = $args['id'] ?? null;

        if (!$negotiationId) {
            $_SESSION['negotiation_error'] = 'Brakujące dane.';
            return $response->withHeader('Location', '/')->withStatus(400);
        }

        $db = $this->container->get('db');

        // Pobranie negocjacji
        $stmt = $db->prepare("
        SELECT n.*, n.listing_id
        FROM negotiations n
        WHERE n.id = :negotiation_id
        ");
        $stmt->execute(['negotiation_id' => $negotiationId]);
        $negotiation = $stmt->fetch();

        if (!$negotiation) {
            $_SESSION['negotiation_error'] = 'Negocjacja nie została znaleziona.';
            return $response->withHeader('Location', '/')->withStatus(404);
        }

        // Pobranie ogłoszenia
        $stmt = $db->prepare("SELECT * FROM listings WHERE id = :id");
        $stmt->execute(['id' => $negotiation['listing_id']]);
        $listing = $stmt->fetch();

        if (!$listing) {
            $_SESSION['negotiation_error'] = 'Ogłoszenie nie zostało znalezione.';
            return $response->withHeader('Location', '/')->withStatus(404);
        }

        // Sprawdzenie, czy aktualny użytkownik jest właścicielem ogłoszenia
        $currentUserId = $_SESSION['user_id'] ?? null;
        if ($listing['user_id'] != $currentUserId) {
            $_SESSION['negotiation_error'] = 'Nie masz uprawnień do akceptacji tej oferty.';
            return $response->withHeader('Location', '/')->withStatus(403);
        }

        // Rozpoczęcie transakcji
        $db->beginTransaction();

        try {
            // Zablokowanie ogłoszenia do aktualizacji
            $stmt = $db->prepare("SELECT status FROM listings WHERE id = :id FOR UPDATE");
            $stmt->execute(['id' => $negotiation['listing_id']]);
            $listing = $stmt->fetch();

            if ($listing['status'] !== 'open') {
                $db->rollBack();
                $_SESSION['negotiation_error'] = 'To ogłoszenie jest już zamknięte.';
                return $response->withHeader('Location', '/negocjacje/start/' . $negotiation['listing_id'])->withStatus(403);
            }

            // Aktualizacja statusu negocjacji na 'accepted'
            $stmt = $db->prepare("UPDATE negotiations SET status = 'accepted', updated_at = NOW() WHERE id = :id");
            $stmt->execute(['id' => $negotiationId]);

            // Ustawienie statusu ogłoszenia na 'closed'
            $stmt = $db->prepare("UPDATE listings SET status = 'closed' WHERE id = :id");
            $stmt->execute(['id' => $negotiation['listing_id']]);

            // Odrzucenie pozostałych negocjacji dla tego ogłoszenia
            $stmt = $db->prepare("UPDATE negotiations SET status = 'rejected' WHERE listing_id = :listing_id AND id != :id");
            $stmt->execute([
                'listing_id' => $negotiation['listing_id'],
                'id' => $negotiationId
            ]);

            // Zatwierdzenie transakcji
            $db->commit();

            $_SESSION['negotiation_success'] = 'Oferta została zaakceptowana, ogłoszenie jest teraz zamknięte.';
            return $response->withHeader('Location', '/negocjacje/start/' . $negotiation['listing_id'])->withStatus(302);
        } catch (\PDOException $e) {
            // Wycofanie transakcji w razie błędu
            $db->rollBack();

            $_SESSION['negotiation_error'] = 'Błąd podczas akceptacji oferty.';
            return $response->withHeader('Location', '/negocjacje/start/' . $negotiation['listing_id'])->withStatus(500);
        }
    }


    // Odrzucenie oferty
    public function rejectOffer(Request $request, Response $response, $args): Response
    {
        $negotiationId = $args['id'] ?? null;

        if (!$negotiationId) {
            $_SESSION['negotiation_error'] = 'Brakujące dane.';
            return $response->withHeader('Location', '/')->withStatus(400);
        }

        $db = $this->container->get('db');

        // Pobranie negocjacji
        $stmt = $db->prepare("
            SELECT n.*, n.listing_id
            FROM negotiations n
            WHERE n.id = :negotiation_id
        ");
        $stmt->execute(['negotiation_id' => $negotiationId]);
        $negotiation = $stmt->fetch();

        if (!$negotiation) {
            $_SESSION['negotiation_error'] = 'Negocjacja nie została znaleziona.';
            return $response->withHeader('Location', '/')->withStatus(404);
        }

        // Pobranie ogłoszenia
        $stmt = $db->prepare("SELECT * FROM listings WHERE id = :id");
        $stmt->execute(['id' => $negotiation['listing_id']]);
        $listing = $stmt->fetch();

        if (!$listing) {
            $_SESSION['negotiation_error'] = 'Ogłoszenie nie zostało znalezione.';
            return $response->withHeader('Location', '/')->withStatus(404);
        }

        // Sprawdzenie, czy aktualny użytkownik jest właścicielem ogłoszenia
        $currentUserId = $_SESSION['user_id'] ?? null;
        if ($listing['user_id'] != $currentUserId) {
            $_SESSION['negotiation_error'] = 'Nie masz uprawnień do akceptacji tej oferty.';
            return $response->withHeader('Location', '/')->withStatus(403);
        }

        try {
            // Aktualizacja statusu negocjacji na 'rejected'
            $stmt = $db->prepare("UPDATE negotiations SET status = 'rejected', updated_at = NOW() WHERE id = :id");
            $stmt->execute(['id' => $negotiationId]);

            $_SESSION['negotiation_success'] = 'Oferta została odrzucona.';
            // Przekierowanie na stronę z formularzem
            return $response->withHeader('Location', '/negocjacje/start/' . $negotiation['listing_id'])->withStatus(302);
        } catch (\PDOException $e) {
            $_SESSION['negotiation_error'] = 'Błąd podczas odrzucania oferty.';
            return $response->withHeader('Location', '/negocjacje/start/' . $negotiation['listing_id'])->withStatus(500);
        }
    }
}
