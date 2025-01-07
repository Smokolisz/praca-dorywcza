<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class ContactController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    // Wyświetlanie strony Kontakt
    public function show(Request $request, Response $response, $args): Response
    {
        $view = $this->container->get('view');
        $output = $view->render('footer-links/contact', [], 'main');
        $response->getBody()->write($output);
        return $response;
    }

    // Obsługa formularza kontaktowego
    public function send(Request $request, Response $response, $args): Response
{
    $data = $request->getParsedBody();
    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $queryType = $data['query_type'] ?? '';
    $message = $data['message'] ?? '';

    if (empty($name) || empty($email) || empty($queryType) || empty($message)) {
        return $response->withStatus(400)->write('Wszystkie pola są wymagane!');
    }

    // Logika obsługi np. zapis do bazy danych lub wysyłanie e-maila
    // Można też dodać specyficzną obsługę w zależności od $queryType

    return $response->withStatus(200);
}

}
