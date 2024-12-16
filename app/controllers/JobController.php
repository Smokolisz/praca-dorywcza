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
        $isEmployer = $job['employer_id'] == $_SESSION['user_id'];

        // Pobierz wszystkie kontrakty związane z ogłoszeniem
        $stmt = $db->prepare("
            SELECT * 
            FROM contracts 
            WHERE job_id = :job_id
        ");
        $stmt->bindParam(':job_id', $args['id'], PDO::PARAM_INT);
        $stmt->execute();
        $contracts = $stmt->fetchAll();

        // Sprawdź, czy użytkownik już wysłał kontrakt dla tego ogłoszenia
        $stmt = $db->prepare("
            SELECT COUNT(*) AS count
            FROM contracts
            WHERE job_id = :job_id AND user_id = :user_id
        ");
        $stmt->bindParam(':job_id', $args['id'], PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $contractExists = $stmt->fetch()['count'] > 0;

        $view = $this->container->get('view');
        $output = $view->render('preview/index', [
            'job' => $job,
            'contracts' => $contracts,
            'isEmployer' => $isEmployer,
            'contractExists' => $contractExists
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
    $stmt = $db->prepare("SELECT employer_id FROM listings WHERE id = :job_id");
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
    $stmt->bindParam(':employer_id', $employer['employer_id'], PDO::PARAM_INT);

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
}
