<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class LogoutController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function logout(Request $request, Response $response, $args): Response
    {
        // Wyczyść sesję
        session_unset();
        session_destroy();

        // Przekierowanie po wylogowaniu
        return $response->withHeader('Location', '/')->withStatus(302);
    }
}
