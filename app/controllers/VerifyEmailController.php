<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class VerifyEmailController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function index(Request $request, Response $response, $args): Response
    {
        $token = $args['token'];

        if(!$token) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $db = $this->container->get('db');

        $sql = "SELECT * FROM users WHERE token = :token";
        $stmt = $db->prepare($sql);
        $user = $stmt->execute(['token' => $token]);

        if(!$user) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }


        // verify user
        $sql = "UPDATE users SET verified = 1 WHERE token = :token";
        $stmt = $db->prepare($sql);
        $stmt->execute(['token' => $token]);

        $view = $this->container->get('view');
        $output = $view->render('verify-email/index', [], 'main');

        $response->getBody()->write($output);
        return $response;
    }
}
