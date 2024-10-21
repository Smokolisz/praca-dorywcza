<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class ListingController
{
    protected $container;

    // Konstruktor, który otrzymuje pojemnik DI
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    // Wyświetlanie formularza dodawania ogłoszeń
    public function showAddListingForm(Request $request, Response $response, $args): Response
    {
        // Renderowanie widoku formularza za pomocą widoku
        $view = $this->container->get('view');
        $output = $view->render('listing/add_listing', [], 'main');  // Zastosuj układ 'main' lub inny

        $response->getBody()->write($output);
        return $response;
    }

    // Obsługa przesyłania formularza
    public function submitListing(Request $request, Response $response, $args): Response
    {
        // Pobieranie danych z formularza
        $data = $request->getParsedBody();

        // Tutaj możesz dodać walidację i logikę przetwarzania danych
        // Na przykład walidacja formularza, zapis do bazy danych itp.
        
        // Przykładowa odpowiedź, po przetworzeniu formularza
        $response->getBody()->write("Ogłoszenie zostało dodane pomyślnie!");

        // Zwróć odpowiedź
        return $response;
    }
}
