<?php

use Slim\Factory\AppFactory;
use DI\Container;
use App\Core\View;
use App\Middleware\NotificationMiddleware;
use App\Services\NotificationService;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Level;

require __DIR__ . '/../../vendor/autoload.php';

// Załaduj konfigurację bazy danych
$dbConfig = require __DIR__ . '/../config/database.php';
$mailConfig = require __DIR__ . '/../config/mail.php';
$appConfig = require __DIR__ . '/../config/app.php';

// Utwórz kontener
$container = new Container();

// Ustaw kontener w AppFactory
AppFactory::setContainer($container);

// Utwórz aplikację
$app = AppFactory::create();

// Konfiguracja połączenia z bazą danych w kontenerze
$container->set('db', function () use ($dbConfig) {
    $db = $dbConfig['db'];
    $dsn = "{$db['driver']}:host={$db['host']};dbname={$db['database']};charset={$db['charset']}";
    $pdo = new PDO($dsn, $db['username'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
});

$container->set('view', function() {
    return new View(__DIR__ . '/../Resources/Views/');
});

$container->set('logger', function () {
    $logger = new Logger('app_logger');

    $logger->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log', Level::Debug));

    return $logger;
});

$container->set('app-config', function () use ($appConfig) {
    return $appConfig;
});

$container->set('mailService', function () use ($mailConfig) {
    $host = $mailConfig['mail']['host'];
    $username = $mailConfig['mail']['username'];
    $password = $mailConfig['mail']['password'];
    $port = $mailConfig['mail']['port'];

    return new \App\Services\MailService($host, $username, $password, $port);
});

$container->set('notificationService', function () use ($container) {
    return new NotificationService($container->get('db'));
});

$app->add(NotificationMiddleware::class);

$app->addErrorMiddleware(true, true, true)
    ->setDefaultErrorHandler(function ($request, \Throwable $exception, bool $displayErrorDetails) use ($container) {
        $logger = $container->get('logger');
        $logger->error($exception->__toString());
        throw $exception;
});

// Załaduj plik z trasami
(require __DIR__ . '/../routes/routes.php')($app);
