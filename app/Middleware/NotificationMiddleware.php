<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Container\ContainerInterface;

class NotificationMiddleware implements MiddlewareInterface
{
    protected $container;
    protected $notificationService;
    protected $view;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->notificationService = $container->get('notificationService');
        $this->view = $container->get('view');
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $userId = $_SESSION['user_id'] ?? null;

        if ($userId) {
            $notifications = $this->notificationService->getNotifications($userId);
            $unreadCount = $this->notificationService->getUnreadCount($userId);

            // Użyj nowej metody addGlobal
            $this->view->addGlobal('unreadCount', $unreadCount);
            $this->view->addGlobal('notifications', $notifications);
        }

        return $handler->handle($request);
    }
}
