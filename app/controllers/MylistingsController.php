<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class MyListingsController
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
            return $response->withStatus(403)->write('Access denied. Please log in.');
        }

        $query = "
            SELECT id, job_type, description, payment, payment_type, city, address 
            FROM listings
            WHERE user_id = :user_id
        ";
        $stmt = $db->prepare($query);
        $stmt->execute(['user_id' => $userId]);
        $listings = $stmt->fetchAll();

        $view = $this->container->get('view');
        $output = $view->render('mylistings/index', ['listings' => $listings], 'main');


        $response->getBody()->write($output);
        return $response;
    }
}
