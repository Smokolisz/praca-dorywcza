<?php

use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\RegisterController;
use Slim\App;

return function (App $app) {

    // strona główna
    $app->get('/', [HomeController::class, 'index']);

    // rejestracja
    $app->get('/zarejestruj-sie', [RegisterController::class, 'index']);
    $app->post('/zarejestruj-sie', [RegisterController::class, 'store']);

    // logowanie
    $app->get('/zaloguj-sie', [LoginController::class, 'index']);
    $app->post('/zaloguj-sie', [LoginController::class, 'login']);

    // moje konto
    $app->get('/konto', function ($request, $response, array $args) {
        // Dewid stwórz tu swój kontroller konta użytkownika
        $response->getBody()->write("moje konto");
        return $response;
    });
};
