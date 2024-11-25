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
    }

    public function show(Request $request, Response $response, $args): Response
    {
        $jobId = (int)$args['jobId'];

        $sql = <<<SQL
        SELECT * FROM listings
        WHERE id = :job_id
        SQL;
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'job_id' => $jobId
        ]);
        $job = $stmt->fetchAll(PDO::FETCH_OBJ);

        $view = $this->container->get('view');
        $output = $view->render('chat/show', ['job' => $job], 'main');
        $response->getBody()->write($output);
        return $response;
    }
}
