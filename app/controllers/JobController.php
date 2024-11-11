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

    public function index(Request $request, Response $response, $args)
    {
        $db = $this->container->get('db');
        $stmt = $db->prepare("SELECT * FROM listings WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $args['id'], PDO::PARAM_INT);
        $stmt->execute();
        $job = $stmt->fetch();

        if (!$job) {
            $response->getBody()->write("Ogłoszenie nie zostało znalezione.");
            return $response->withStatus(404);
        }

        $view = $this->container->get('view');
        $output = $view->render('preview/index', ['job' => $job], 'main');

        $response->getBody()->write($output);
        return $response;
    }
}
