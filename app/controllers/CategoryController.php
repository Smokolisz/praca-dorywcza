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
    
        $category = null;
        $listings = [];
    
        // Pobranie wszystkich kategorii
        $query = "SELECT * FROM categories";
        $stmt = $db->query($query);
        $categories = $stmt->fetchAll();
    
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
            'category' => $category,
            'listings' => $listings,
        ], 'main');
    
        $response->getBody()->write($output);
        return $response;
    }
    

    // Obsługa wyświetlania kategorii na podstawie dynamicznej trasy
    public function show(Request $request, Response $response, $args): Response
    {
        $db = $this->container->get('db');
        $categoryName = $args['name']; // Pobranie nazwy kategorii z parametrów trasy
    
        // Pobranie szczegółów kategorii na podstawie nazwy
        $query = "SELECT * FROM categories WHERE LOWER(name) = LOWER(:name)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $categoryName, \PDO::PARAM_STR);
        $stmt->execute();
        $category = $stmt->fetch();
    
        if (!$category) {
            return $response->withStatus(404)->write('Category not found');
        }
    
        // Pobranie ogłoszeń powiązanych z kategorią
        $query = "
            SELECT job_type, description, payment_type, employer_name, city
            FROM listings
            WHERE category_id = :category_id
        ";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':category_id', $category['id'], \PDO::PARAM_INT); // Pobierz ID z wyszukanego rekordu kategorii
        $stmt->execute();
        $listings = $stmt->fetchAll();
    
        $view = $this->container->get('view');
    
        // Renderowanie widoku szczegółowego kategorii z ogłoszeniami
        $output = $view->render('category/show', [
            'category' => $category, // Szczegóły kategorii
            'listings' => $listings  // Lista ogłoszeń
        ], 'main');
    
        $response->getBody()->write($output);
        return $response;
    }
    
}
