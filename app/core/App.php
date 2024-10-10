<?php

use Slim\Factory\AppFactory;
use Psr\Container\ContainerInterface;
use DI\Container;
use App\Core\View;

require __DIR__ . '/../../vendor/autoload.php';

// Załaduj konfigurację bazy danych
$settings = require __DIR__ . '/../config/database.php';

// Utwórz kontener
$container = new Container();

// Ustaw kontener w AppFactory
AppFactory::setContainer($container);

// Utwórz aplikację
$app = AppFactory::create();

// Konfiguracja połączenia z bazą danych w kontenerze
$container->set('db', function (ContainerInterface $c) use ($settings) {
    $db = $settings['db'];
    $dsn = "{$db['driver']}:host={$db['host']};dbname={$db['database']};charset={$db['charset']}";
    $pdo = new PDO($dsn, $db['username'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
});

$container->set('view', function() {
    return new View(__DIR__ . '/../resources/views/');
});

// Załaduj plik z trasami
(require __DIR__ . '/../routes/routes.php')($app);
