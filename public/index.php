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
$controller = new $controllerName();

// Check if method exists
if (!method_exists($controller, $methodName)) {
    $methodName = 'notFound';
    $params = [];
}

// Call the method with parameters
call_user_func_array([$controller, $methodName], $params);
