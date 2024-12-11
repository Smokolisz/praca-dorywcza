<?php

namespace App\Controllers;

use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class SearchController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function index(Request $request, Response $response, $args): Response
    {
        $view = $this->container->get('view');
        $db = $this->container->get('db');

        $fields = [];
        $sql = <<<SQL
        SELECT *, listings.id AS job_id FROM listings
        WHERE STATUS = 'open'
        SQL;

        $searchString = $_GET['q'] ?? '';
        if ($searchString) {
            $sql .= <<<SQL
            AND (
            job_type LIKE :q1 OR 
            requirements LIKE :q2 OR 
            description LIKE :q3 
            )
            SQL;
            $fields['q1'] = '%' . $searchString . '%';
            $fields['q2'] = '%' . $searchString . '%';
            $fields['q3'] = '%' . $searchString . '%';
        }

        // Filtr po rodzaju stawki (payment_type)
        if (!empty($_GET['payment_type'])) {
            $sql .= " AND payment_type = :payment_type";
            $fields['payment_type'] = $_GET['payment_type'];
        }

        // Filtr po mieście (city)
        if (!empty($_GET['city'])) {
            $sql .= " AND city LIKE :city";
            $fields['city'] = '%' . $_GET['city'] . '%';
        }

        // Filtr wynagrodzenia od (payment_from)
        if (!empty($_GET['payment_from'])) {
            $sql .= " AND payment >= :payment_from";
            $fields['payment_from'] = $_GET['payment_from'];
        }

        // Filtr wynagrodzenia do (payment_to)
        if (!empty($_GET['payment_to'])) {
            $sql .= " AND payment <= :payment_to";
            $fields['payment_to'] = $_GET['payment_to'];
        }

        // Filtr szacowanego czasu od (estimated_time_from)
        if (!empty($_GET['estimated_time_from'])) {
            $sql .= " AND estimated_time >= :estimated_time_from";
            $fields['estimated_time_from'] = $_GET['estimated_time_from'];
        }

        // Filtr szacowanego czasu do (estimated_time_to)
        if (!empty($_GET['estimated_time_to'])) {
            $sql .= " AND estimated_time <= :estimated_time_to";
            $fields['estimated_time_to'] = $_GET['estimated_time_to'];
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($fields);
        $jobs = $stmt->fetchAll(PDO::FETCH_OBJ);

        $data = [
            'jobs' => $jobs
        ];

        $output = $view->render('search/index', $data, 'main');
        $response->getBody()->write($output);
        return $response;
    }
}
