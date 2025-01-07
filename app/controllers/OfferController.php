<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class OfferController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    // Wyświetlanie strony "Oferta dla Firm"
    public function show(Request $request, Response $response, $args): Response
    {
        $view = $this->container->get('view');
        $output = $view->render('footer-links/offer', [], 'main');
        $response->getBody()->write($output);
        return $response;
    }
}
