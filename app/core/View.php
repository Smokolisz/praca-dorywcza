<?php

namespace App\Core;

class View
{
    protected $path;
    protected $data = [];
    protected $sections = [];
    protected $currentSection;

    public function __construct($path = '../resources/views/')
    {
        $this->path = $path;
    }

    public function render($view, $data = [], $layout = null)
    {
        $this->data = $data;

        $viewPath = $this->path . $view . '.php';

        if (file_exists($viewPath)) {
            // Przekazujemy dane do widoku
            extract($this->data);

            // Definiujemy funkcje sekcji
            $this->sections = [];
            $this->currentSection = null;

            // Start buforowania wyjścia
            ob_start();
            require $viewPath;
            $content = ob_get_clean();

            // Jeśli podano layout
            if ($layout) {
                $layoutPath = $this->path . 'layout/' . $layout . '.php';

                if (file_exists($layoutPath)) {
                    // Przekazujemy dane do layoutu
                    extract($this->data);

                    // Zmienna $content i sekcje będą dostępne w layout
                    ob_start();
                    require $layoutPath;
                    return ob_get_clean();
                } else {
                    throw new \Exception("Layout $layout nie został znaleziony.");
                }
            } else {
                return $content;
            }
        } else {
            throw new \Exception("Widok $view nie został znaleziony.");
        }
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
