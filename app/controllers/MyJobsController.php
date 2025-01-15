<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class MyJobsController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function index(Request $request, Response $response, $args): Response
    {
        $db = $this->container->get('db');

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $response->getBody()->write('Access denied. Please log in.');
            return $response->withStatus(403);
        }

        $query = "
            SELECT l.id, l.job_type, l.description, l.payment, l.payment_type, l.city, l.address, c.created_at AS accepted_date
            FROM contracts c
            JOIN listings l ON c.job_id = l.id
            WHERE c.user_id = :user_id AND c.status = 'accepted' AND l.listing_status = 'active'
        ";
        $stmt = $db->prepare($query);
        $stmt->execute(['user_id' => $userId]);
        $jobs = $stmt->fetchAll();

        $view = $this->container->get('view');
        $output = $view->render('myjobs/index', ['jobs' => $jobs], 'main');

        $response->getBody()->write($output);
        return $response;
    }
}
