<?php
/**
 * Database Connection Class
 * Singleton pattern for database connection management
 */

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch(PDOException $e) {
            // Log the error
            error_log("Database Connection Failed: " . $e->getMessage());
            
            // Show user-friendly error page
            http_response_code(503);
            $errorMessage = "Error de Conexión a la Base de Datos";
            $errorDetails = "No se pudo conectar a la base de datos. Por favor, contacte al administrador del sistema.";
            
            // In development mode, log more details but don't expose them to users
            if (getenv('APP_ENV') === 'development') {
                error_log("Database Error Details: " . $e->getMessage());
                $errorDetails .= "<br><br><em>Los detalles del error se han registrado en el log del sistema.</em>";
            }
            
            // Display error page
            echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error de Conexión</title>
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
        <h1>' . htmlspecialchars($errorMessage) . '</h1>
        <p>' . $errorDetails . '</p>
    </div>
</body>
</html>';
            exit();
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
