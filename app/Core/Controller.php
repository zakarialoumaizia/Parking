<?php
class Controller {
    public function model($model) {
        require_once '../app/Models/' . $model . '.php';
        return new $model();
    }

    public function view($view, $data = []) {
        // Extract data for view
        extract($data); 
        
        if (file_exists('../app/Views/' . $view . '.php')) {
            require_once '../app/Views/' . $view . '.php';
        } else {
            die("View does not exist: " . $view);
        }
    }

    protected function redirect($path) {
        // If path is full URL, redirect directly
        if (strpos($path, 'http') === 0) {
            header("Location: " . $path);
        } else {
            // Otherwise use the url helper to get the correct path
            header("Location: " . url($path));
        }
        exit();
    }
}
