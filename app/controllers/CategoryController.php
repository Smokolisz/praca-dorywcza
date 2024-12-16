<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class CategoryController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    // Obsługa wyszukiwania kategorii oraz wyświetlania ogłoszeń
    public function index(Request $request, Response $response, $args): Response
    {
        $db = $this->container->get('db');
        $queryParams = $request->getQueryParams();
        $categoryName = $queryParams['category_name'] ?? null;

        $userId = $_SESSION['user_id'] ?? null; // Pobranie ID użytkownika

        $category = null;
        $listings = [];

        // Pobranie wszystkich kategorii
        $query = "SELECT * FROM categories";
        $stmt = $db->query($query);
        $categories = $stmt->fetchAll();

        // Pobranie ulubionych kategorii użytkownika
        $favoriteCategories = [];
        if ($userId) {
            $query = "SELECT category_id FROM user_favorites WHERE user_id = :user_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $userId, \PDO::PARAM_INT);
            $stmt->execute();
            $favoriteCategories = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        }

        if (!empty($categoryName)) {
            // Wyszukiwanie kategorii po nazwie
            $query = "SELECT * FROM categories WHERE LOWER(name) = LOWER(:name)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':name', $categoryName, \PDO::PARAM_STR);
            $stmt->execute();
            $category = $stmt->fetch();

            if ($category) {
                // Pobranie ogłoszeń powiązanych z kategorią
                $query = "
                    SELECT job_type, description, payment_type, employer_name, city
                    FROM listings
                    WHERE category_id = :category_id
                ";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':category_id', $category['id'], \PDO::PARAM_INT);
                $stmt->execute();
                $listings = $stmt->fetchAll();
            }
        }

        // Renderowanie widoku
        $view = $this->container->get('view');
        $output = $view->render('category/index', [
            'categories' => $categories,
            'favoriteCategories' => $favoriteCategories,
            'category' => $category,
            'listings' => $listings,
        ], 'main');

        $response->getBody()->write($output);
        return $response;
    }

    // Dodawanie kategorii do ulubionych
    public function addFavorite(Request $request, Response $response, $args): Response
    {
        $db = $this->container->get('db');
        $userId = $_SESSION['user_id'] ?? null;
        $categoryId = $args['id'];

        if (!$userId) {
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        $query = "INSERT INTO user_favorites (user_id, category_id) VALUES (:user_id, :category_id)
                  ON DUPLICATE KEY UPDATE user_id = user_id";
        $stmt = $db->prepare($query);
        $stmt->execute([':user_id' => $userId, ':category_id' => $categoryId]);

        return $response->withHeader('Location', '/kategoria')->withStatus(302);
    }

    // Usuwanie kategorii z ulubionych
    public function removeFavorite(Request $request, Response $response, $args): Response
    {
        $db = $this->container->get('db');
        $userId = $_SESSION['user_id'] ?? null;
        $categoryId = $args['id'];

        if (!$userId) {
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        $query = "DELETE FROM user_favorites WHERE user_id = :user_id AND category_id = :category_id";
        $stmt = $db->prepare($query);
        $stmt->execute([':user_id' => $userId, ':category_id' => $categoryId]);

        return $response->withHeader('Location', '/kategoria')->withStatus(302);
    }
}
