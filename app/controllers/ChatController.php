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

    public function show(Request $request, Response $response, $args): Response
    {
        $jobId = (int)$args['jobId'];

        $sql = <<<SQL
        SELECT *, listings.id AS job_id FROM listings
        INNER JOIN users ON listings.user_id = users.id
        WHERE listings.id = :job_id
        SQL;
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'job_id' => $jobId
        ]);
        $job = $stmt->fetch(PDO::FETCH_OBJ);

        if(!$job) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }


        $chat = $this->getChat($job);

        if(!$chat) {
            // zalogowany użytkownik to zleceniobiorca i rozpoczyna nowy czat
            $this->createChat($job);
            $chat = $this->getChat($job);
        }

        $data = [
            'job' => $job,
            'chat' => $chat,
        ];

        $view = $this->container->get('view');
        $output = $view->render('chat/show', $data, 'main');
        $response->getBody()->write($output);
        return $response;
    }

    public function getMessages(Request $request, Response $response, $args) : Response
    {
        $jobId = (int)$args['jobId'];

        $sql = <<<SQL
        SELECT cm.*
        FROM chat_messages cm
        JOIN chat c ON cm.chat_id = c.id
        WHERE c.job_id = :job_id AND (c.employer_id = :user_id1 OR c.worker_id = :user_id2)
        ORDER BY cm.date_added ASC
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'job_id' => $jobId,
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

        // dd($_SESSION['first_name'], $sql, $data, $chat);


        return $chat;
    }
}
