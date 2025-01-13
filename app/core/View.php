<?php

namespace App\Core;

class View
{
    protected $path;
    protected $data = [];
    protected $sections = [];
    protected $currentSection;
    protected $globals = []; // Dodaj tablicę na zmienne globalne

    public function __construct($path = '../resources/views/')
    {
        $this->path = $path;
    }

    // Dodaj metodę do ustawiania zmiennych globalnych
    public function addGlobal($name, $value)
    {
        $this->globals[$name] = $value;
    }

    public function render($view, $data = [], $layout = null)
    {
        // Połącz zmienne globalne z lokalnymi danymi
        $this->data = array_merge($this->globals, $data);

        $viewPath = $this->path . $view . '.php';

        if (file_exists($viewPath)) {
            extract($this->data);

            $this->sections = [];
            $this->currentSection = null;

            ob_start();
            require $viewPath;
            $content = ob_get_clean();

            if ($layout) {
                $layoutPath = $this->path . 'layout/' . $layout . '.php';
                if (file_exists($layoutPath)) {
                    ob_start();
                    require $layoutPath;
                    return ob_get_clean();
                }
            }

            return $content;
        }

        throw new \Exception("View $view not found");
    }

    // Funkcje do obsługi sekcji
    public function startSection($name)
    {
        $this->currentSection = $name;
        ob_start();
    }

    public function endSection()
    {
        $this->sections[$this->currentSection] = ob_get_clean();
    }

    public function section($name)
    {
        return isset($this->sections[$name]) ? $this->sections[$name] : '';
    }
}
