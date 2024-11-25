<?php

namespace App\Services;

use PDO;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ChatServer implements MessageComponentInterface
{
    protected $clients;
    protected $db;

    public function __construct() {
        $this->clients = new \SplObjectStorage;

        // Ustawienie połączenia z bazą danych
        $this->db = new PDO('mysql:host=localhost;dbname=swift-jobs', 'root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Przechowywanie nowego połączenia
        $this->clients->attach($conn);

        // Pobranie parametrów z URL (user_id i chat_id)
        $querystring = $conn->httpRequest->getUri()->getQuery();
        parse_str($querystring, $params);
        $conn->user_id = $params['user_id'];
        $conn->chat_id = $params['chat_id'];
    }

    public function onMessage(ConnectionInterface $from, $msg) : void
    {
        $data = json_decode($msg, true);

        switch($data['type']) {
            case 'message':
                $this->addMessage($from, $data);
                break;
            case 'typing':
                $this->notifyOtherPersonTyping($from, $data);
                break;
        }
    }

    private function addMessage(ConnectionInterface $from, array $data)
    {
        $chat_id = $from->chat_id;
        $user_id = $from->user_id;
        $text = $data['text'];
        if(!$text) return;

        // Zapisanie wiadomości do bazy danych
        $stmt = $this->db->prepare("INSERT INTO chat_messages (chat_id, user_id, text, date_added) VALUES (:chat_id, :user_id, :text, NOW())");
        $stmt->execute([
            ':chat_id' => $chat_id,
            ':user_id' => $user_id,
            ':text' => $text
        ]);

        // Wysłanie wiadomości do odbiorcy jeśli jest połączony
        foreach ($this->clients as $client) {
            if ($client->chat_id == $chat_id && $client->user_id != $user_id) {
                $client->send(json_encode([
                    'type' => 'message',
                    'user_id' => $user_id,
                    'text' => $text,
                    'date_added' => date('Y-m-d H:i:s')
                ]));
            }
        }
    }

    private function notifyOtherPersonTyping(ConnectionInterface $from, array $data)
    {
        $chat_id = $from->chat_id;
        $user_id = $from->user_id;
        foreach ($this->clients as $client) {
            if ($client->chat_id == $chat_id && $client->user_id != $user_id) {
                $client->send(json_encode([
                    'type' => 'typing',
                    'status' => $data['status'],
                    'user_id' => $user_id,
                ]));
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Usunięcie połączenia
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        // Obsługa błędów
        $conn->close();
    }
}
