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

    public function index(Request $request, Response $response, $args)
    {
        // Renderowanie widoku z layoutem 'main'
        $view = $this->container->get('view');
        $output = $view->render('home/index', [], 'main');

        $response->getBody()->write($output);
        return $response;
    }
}
