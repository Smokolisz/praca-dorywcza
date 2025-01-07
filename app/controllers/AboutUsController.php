<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class AboutUsController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    // Wyświetlanie strony O nas
    public function show(Request $request, Response $response, $args): Response
    {
        $view = $this->container->get('view');
        $output = $view->render('footer-links/aboutus', [], 'main');
        $response->getBody()->write($output);
        return $response;
    }
}
