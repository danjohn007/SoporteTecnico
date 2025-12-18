<?php
/**
 * Base Controller
 * All controllers extend from this base class
 */

class BaseController {
    
    /**
     * Load a view file
     */
    protected function view($view, $data = []) {
        // Extract data array to variables
        extract($data);
        
        // Check if view file exists
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View not found: " . $view);
        }
    }
    
    /**
     * Load a model
     */
    protected function model($model) {
        $modelFile = __DIR__ . '/../models/' . $model . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        }
        die("Model not found: " . $model);
    }
    
    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200) {
        jsonResponse($data, $statusCode);
    }
    
    /**
     * Handle 404 Not Found
     */
    public function notFound() {
        http_response_code(404);
        $this->view('errors/404', [
            'title' => 'Página No Encontrada'
        ]);
    }
    
    /**
     * Get POST data
     */
    protected function getPost($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }
    
    /**
     * Get GET data
     */
    protected function getQuery($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }
    
    /**
     * Check if request is POST
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Check if request is AJAX
     */
    protected function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
