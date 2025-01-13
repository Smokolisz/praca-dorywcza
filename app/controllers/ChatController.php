<?php

namespace App\Controllers;

use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class ChatController
{
    protected $container;
    private PDO $db;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->db = $this->container->get('db');

        // tylko dla zalogowanych
        if (!isset($_SESSION['user_id'])) {
            header('Location: /zaloguj-sie');
            exit;
        }
    }

    public function index(Request $request, Response $response, $args): Response
    {
        $sql = <<<SQL
        SELECT * FROM chat
        WHERE 
            employer_id = :user_id1 OR
            worker_id = :user_id2
        ORDER BY chat.id DESC
        LIMIT 1
        SQL;
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id1' => $_SESSION['user_id'],
            'user_id2' => $_SESSION['user_id'],
        ]);
        $chat = $stmt->fetch(PDO::FETCH_OBJ);

        if($chat) {
            // przekieruj do ostatniego czatu
            return $response->withHeader('Location', '/czat/'.$chat->id)
                    ->withStatus(302);
        }

        // brak czatów
        $view = $this->container->get('view');
        $output = $view->render('chat/index', [], 'main');
        $response->getBody()->write($output);
        return $response;
    }

    public function show(Request $request, Response $response, $args): Response
    {
        $chatId = (int)$args['chatId'];

        $sql = <<<SQL
        SELECT 
            chat.id AS chat_id,
            listings.id AS job_id, 
            listings.*, 
            users.first_name,
            users.last_name,
            other_user.first_name AS other_user_first_name,
            other_user.last_name AS other_user_last_name
        FROM chat
        INNER JOIN listings ON listings.id = chat.job_id
        INNER JOIN users ON listings.user_id = users.id
        INNER JOIN users AS other_user ON other_user.id = CASE
            WHEN chat.employer_id = listings.user_id THEN chat.worker_id
            ELSE chat.employer_id
        END
        WHERE chat.id = :chat_id
        LIMIT 1
        SQL;
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'chat_id' => $chatId
        ]);
        $chat = $stmt->fetch(PDO::FETCH_OBJ);

        if(!$chat) {
            return $response->withHeader('Location', '/czat')->withStatus(302);
        }

        // pobierz historie czatów
        $sql = <<<SQL
        SELECT 
            c.*,
            eu.first_name AS employer_first_name,
            eu.last_name AS employer_last_name,
            wu.first_name AS worker_first_name,
            wu.last_name AS worker_last_name,
            ou.id AS other_user_id,
            ou.first_name AS other_user_first_name,
            ou.last_name AS other_user_last_name
        FROM chat c
        LEFT JOIN users eu ON c.employer_id = eu.id
        LEFT JOIN users wu ON c.worker_id = wu.id
        INNER JOIN users ou ON ou.id = CASE 
                                        WHEN c.employer_id = :current_user_id1 THEN c.worker_id
                                        ELSE c.employer_id
                                    END
        WHERE :current_user_id2 IN (c.employer_id, c.worker_id)
        ORDER BY c.id DESC;
        SQL;
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'current_user_id1' => $_SESSION['user_id'],
            'current_user_id2' => $_SESSION['user_id'],
        ]);
        $chatsHistory = $stmt->fetchAll(PDO::FETCH_OBJ);

        $data = [
            'chatsHistory' => $chatsHistory,
            'chat' => $chat,
        ];

        $view = $this->container->get('view');
        $output = $view->render('chat/show', $data, 'main');
        $response->getBody()->write($output);
        return $response;
    }
    
    public function create(Request $request, Response $response, $args): Response
    {
        $jobId = (int)$args['jobId'];

        $sql = <<<SQL
        SELECT *
        FROM listings
        WHERE id = :job_id
        LIMIT 1
        SQL;
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'job_id' => $jobId
        ]);
        $job = $stmt->fetch(PDO::FETCH_OBJ);

        $chat = $this->getChat($job);

        if(!$chat && $job && $job->user_id == $_SESSION['user_id']) {
            // zalogowany użytkownik jest właścicielem ogłoszenia, więc nie może zacząć czatu sam ze sobą
            return $response->withHeader('Location', '/job/'.$job->job_id)->withStatus(302);
        } else if(!$chat) {
            // zalogowany użytkownik to zleceniobiorca i rozpoczyna nowy czat
            $this->createChat($job);
            $chat = $this->getChat($job);
        }

        return $response->withHeader('Location', '/czat/'.$chat->chat_id)->withStatus(302);
    }

    public function getMessages(Request $request, Response $response, $args) : Response
    {
        $chatId = (int)$args['chatId'];

        $sql = <<<SQL
        SELECT cm.*
        FROM chat c
        JOIN chat_messages cm ON c.id = cm.chat_id
        WHERE c.id = :chat_id
        AND (c.employer_id = :user_id1 OR c.worker_id = :user_id2)
        ORDER BY cm.date_added ASC;
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'chat_id' => $chatId,
            'user_id1' => $_SESSION['user_id'],
            'user_id2' => $_SESSION['user_id'],
        ]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($result));
        return $response
                ->withHeader('Content-Type', 'application/json');
    }

    private function createChat(object $job) : void
    {
        // tworzenie nowego chatu:
        $sql = <<<SQL
        INSERT INTO chat (job_id, employer_id, worker_id)
        VALUES (:job_id, :employer_id, :worker_id)
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'job_id' => $job->id,
            'employer_id' => $job->user_id,
            'worker_id' => $_SESSION['user_id']
        ]);
        $stmt->fetch(PDO::FETCH_OBJ);
    }

    private function getChat(object $job) : object|false
    {
        $sql = <<<SQL
            SELECT chat.id as chat_id, job_id, employer_id, worker_id, other_user.first_name AS other_user_first_name FROM chat 
        SQL;

        $data = [
            'job_id' => $job->id,
        ];

        if($job->user_id == $_SESSION['user_id']) {
            // zalogowany użytkownik jest właścicielem ogłoszenia
            $sql .= <<<SQL
            INNER JOIN users other_user ON chat.worker_id = other_user.id
            INNER JOIN users auth_user ON chat.employer_id = auth_user.id
            WHERE chat.job_id = :job_id AND chat.employer_id = :employer_id 
            SQL;

            $data['employer_id'] = $_SESSION['user_id'];
        } else {
            // zalogowany użytkownik pisze jako zleceniobiorca
            $sql .= <<<SQL
            INNER JOIN users other_user ON chat.employer_id = other_user.id
            INNER JOIN users auth_user ON chat.worker_id = auth_user.id
            WHERE chat.job_id = :job_id AND chat.worker_id = :worker_id 
            SQL;

            $data['worker_id'] = $_SESSION['user_id'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        $chat = $stmt->fetch(PDO::FETCH_OBJ);

        return $chat;
    }
}
