<?php

use App\Controllers\HomeController;
use Slim\App;

return function (App $app) {
    $app->get('/', [HomeController::class, 'index']);
};
