<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class JobHistoryController
{
    private $db;
    private $view;
    private $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('db');
        $this->view = $container->get('view');
        $this->container = $container;
    }

    public function createdJobs(Request $request, Response $response): Response
    {
        $sql = <<<SQL
        SELECT listings.*, users.first_name, users.last_name 
        FROM listings
        LEFT JOIN users ON listings.user_id = users.id
        WHERE listings.user_id = :user_id
        ORDER BY listings.created_at DESC
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $_SESSION['user_id']
        ]);
        $listings = $stmt->fetchAll(\PDO::FETCH_OBJ);

        $output = $this->container->get('view')->render('job-history/created', [
            'listings' => $listings
        ], 'main');

        $response->getBody()->write($output);
        return $response;
    }

    public function acceptedJobs(Request $request, Response $response): Response
    {
        $sql = <<<SQL
        SELECT listings.*, users.first_name, users.last_name 
        FROM listings
        LEFT JOIN users ON listings.user_id = users.id
        INNER JOIN contracts ON listings.id = contracts.job_id
        WHERE contracts.user_id = :user_id 
        AND contracts.status = 'accepted'
        ORDER BY listings.created_at DESC
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $_SESSION['user_id']
        ]);
        $listings = $stmt->fetchAll(\PDO::FETCH_OBJ);

        $output = $this->view->render('job-history/accepted', [
            'listings' => $listings
        ], 'main');

        $response->getBody()->write($output);
        return $response;
    }
}
