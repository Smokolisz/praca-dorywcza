<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class NotificationController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    // Pobieranie powiadomień dla zalogowanego użytkownika
    public function index(Request $request, Response $response, $args): Response
    {
        $db = $this->container->get("db");
        $view = $this->container->get("view");

        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            return $response->withHeader('Location', '/zaloguj-sie')->withStatus(302);
        }

        $stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $notifications = $stmt->fetchAll();


        $output = $view->render('account/notifications', ['notifications' => $notifications], "main");
        $response->getBody()->write($output);
        return $response;
    }

    public function createNotification(int $userId, string $type, string $content, ?int $listingId = null): void
    {
        $db = $this->container->get('db');

        // Przygotuj zapytanie SQL
        $stmt = $db->prepare("
        INSERT INTO notifications (user_id, type, content, listing_id) 
        VALUES (:user_id, :type, :content, :listing_id)
    ");

        // Wykonaj zapytanie
        $stmt->execute([
            'user_id' => $userId,
            'type' => $type,
            'content' => $content,
            'listing_id' => $listingId
        ]);
    }


    public function viewDetails(Request $request, Response $response, $args): Response
    {
        $db = $this->container->get("db");
        $data = $request->getParsedBody();
        $notificationId = $data['notification_id'] ?? null;

        if (!$notificationId) {
            return $response->withHeader('Location', '/profil/powiadomienia')->withStatus(302);
        }

        // Zmieniamy status powiadomienia na przeczytane
        $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id");
        $stmt->execute(['id' => $notificationId]);

        // Pobierz szczegóły powiadomienia
        $stmt = $db->prepare("SELECT * FROM notifications WHERE id = :id");
        $stmt->execute(['id' => $notificationId]);
        $notification = $stmt->fetch();

        if (!$notification) {
            return $response->withHeader('Location', '/profil/powiadomienia')->withStatus(302);
        }

        // Przekierowanie w zależności od powiadomienia
        if ($notification['type'] === 'new_favourite_listing' && $notification['listing_id']) {
            $redirectUrl = "/job/" . $notification['listing_id']; // Przekierowanie do podglądu ogłoszenia
        } elseif ($notification['type'] === 'new_message') {
            $redirectUrl = "/chat";
        } elseif ($notification['type'] === 'new_review') {
            $redirectUrl = "/profil/opinie"; // Przekierowanie do opinii
        } elseif ($notification['type'] === 'new_negotiation' && $notification['listing_id']) {
            $redirectUrl = "/negocjacje/start/" . $notification['listing_id']; // Przekierowanie do szczegółów negocjacji
        } elseif ($notification['type'] === 'accepted_negotiation' && $notification['listing_id']) {
            $redirectUrl = "/negocjacje/start/" . $notification['listing_id']; // Przekierowanie do zaakceptowanej negocjacji
        } elseif ($notification['type'] === 'rejected_negotiation' && $notification['listing_id']) {
            $redirectUrl = "/negocjacje/start/" . $notification['listing_id']; // Przekierowanie do odrzuconej negocjacji
        } else {
            $redirectUrl = "/profil/powiadomienia";
        }

        return $response->withHeader('Location', $redirectUrl)->withStatus(302);
    }

    // Oznaczanie powiadomień jako przeczytane
    public function markAsRead(Request $request, Response $response, $args): Response
    {
        $db = $this->container->get("db");

        $userId = $_SESSION['user_id'] ?? null;

        if ($userId) {
            $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
        }

        return $response->withHeader('Location', '/profil/powiadomienia')->withStatus(302);
    }
}
