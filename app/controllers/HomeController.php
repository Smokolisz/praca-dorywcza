<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class HomeController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function index(Request $request, Response $response, $args): Response
    {
        $db = $this->container->get('db');
    
        $query = "
            SELECT l.id, l.job_type, l.description, l.payment, l.payment_type, l.city, l.address 
            FROM listings l
            LEFT JOIN contracts c ON l.id = c.job_id AND c.status = 'accepted'
            WHERE c.id IS NULL
        ";
        $stmt = $db->query($query);
        $listings = $stmt->fetchAll();
    
        $view = $this->container->get('view');
        $output = $view->render('home/index', ['listings' => $listings], 'main');
    
        $response->getBody()->write($output);
        return $response;
    }
    
}
