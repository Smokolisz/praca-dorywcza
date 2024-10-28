<?php

use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\RegisterController;
use App\Controllers\JobController;
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
    
    $app->get('/job/{id}', [JobController::class, 'index']);

};

//tutaj sie dodaje sciezki np.: /login ($app->get('/login', [HomeController::class, 'index']);)