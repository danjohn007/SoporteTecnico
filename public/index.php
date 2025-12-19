<?php
/**
 * Bootstrap / Front Controller
 * Entry point for all requests
 */

// Start session
session_start();

// Load configuration
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Autoloader for classes
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../app/models/' . $class . '.php',
        __DIR__ . '/../app/controllers/' . $class . '.php',
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Helper functions
require_once __DIR__ . '/../app/helpers.php';

// Get URL from query string
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'home';
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Whitelist of allowed controllers for security
$allowedControllers = [
    'home', 'auth', 'dashboard', 'tickets', 'faq', 'chatbot', 
    'admin', 'settings', 'agent', 'notifications'
];

// Determine controller and method
$controllerPath = !empty($url[0]) ? strtolower($url[0]) : 'home';
if (!in_array($controllerPath, $allowedControllers)) {
    $controllerPath = 'home';
}

$controllerName = ucfirst($controllerPath) . 'Controller';
$methodName = isset($url[1]) && !empty($url[1]) ? $url[1] : 'index';
$params = array_slice($url, 2);

// Default to HomeController if controller doesn't exist
$controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';
if (!file_exists($controllerFile)) {
    $controllerName = 'HomeController';
    $methodName = 'notFound';
    $params = [];
}

// Instantiate controller
require_once __DIR__ . '/../app/controllers/' . $controllerName . '.php';

try {
    $controller = new $controllerName();
    
    // Check if method exists
    if (!method_exists($controller, $methodName)) {
        $methodName = 'notFound';
        $params = [];
    }
    
    // Call the method with parameters
    call_user_func_array([$controller, $methodName], $params);
} catch (Exception $e) {
    // Log the error to file
    error_log('Controller error: ' . $e->getMessage(), 3, __DIR__ . '/../logs/php-error.log');
    
    // Show error page
    http_response_code(500);
    echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error del Sistema</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .error-container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 500px; text-align: center; }
        .error-container h1 { color: #dc2626; margin-top: 0; }
        .error-container p { color: #4b5563; line-height: 1.6; }
        .error-container .icon { font-size: 3rem; color: #dc2626; }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="icon">⚠️</div>
        <h1>Error del Sistema</h1>
        <p>Ha ocurrido un error inesperado. Por favor, contacte al administrador del sistema.</p>
    </div>
</body>
</html>';
    exit();
}
