#!/usr/bin/env php
<?php
/**
 * Test Bootstrap Script
 * Tests if the system can bootstrap correctly
 */

// Simulate web environment
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SCRIPT_NAME'] = '/public/index.php';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/';

echo "Testing bootstrap process...\n";
echo "----------------------------\n\n";

// Step 1: Load configuration
echo "1. Loading configuration...\n";
try {
    require_once __DIR__ . '/config/config.php';
    echo "   ✓ Config loaded successfully\n";
    echo "   - BASE_URL: " . BASE_URL . "\n";
    echo "   - DB_NAME: " . DB_NAME . "\n";
    echo "   - DB_HOST: " . DB_HOST . "\n";
} catch (Exception $e) {
    echo "   ✗ Config failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Step 2: Load database
echo "\n2. Loading database connection...\n";
try {
    require_once __DIR__ . '/config/database.php';
    echo "   ✓ Database class loaded\n";
} catch (Exception $e) {
    echo "   ✗ Database failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Step 3: Test database connection
echo "\n3. Testing database connection...\n";
try {
    $db = Database::getInstance();
    echo "   ✓ Database connection successful\n";
    
    // Test a simple query
    $conn = $db->getConnection();
    $stmt = $conn->query("SELECT 1");
    if ($stmt) {
        echo "   ✓ Database query test passed\n";
    }
} catch (Exception $e) {
    echo "   ✗ Database connection failed: " . $e->getMessage() . "\n";
    echo "   Note: This is expected if database is not set up yet\n";
}

// Step 4: Load helpers
echo "\n4. Loading helper functions...\n";
try {
    require_once __DIR__ . '/app/helpers.php';
    echo "   ✓ Helpers loaded successfully\n";
    
    // Test a few helper functions
    $testUrl = url('test');
    echo "   - url('test') = " . $testUrl . "\n";
    
    $testAsset = asset('css/style.css');
    echo "   - asset('css/style.css') = " . $testAsset . "\n";
} catch (Exception $e) {
    echo "   ✗ Helpers failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Step 5: Test autoloader
echo "\n5. Testing class autoloader...\n";
try {
    spl_autoload_register(function ($class) {
        $paths = [
            __DIR__ . '/app/models/' . $class . '.php',
            __DIR__ . '/app/controllers/' . $class . '.php',
        ];
        
        foreach ($paths as $path) {
            if (file_exists($path)) {
                require_once $path;
                return;
            }
        }
    });
    echo "   ✓ Autoloader registered\n";
    
    // Test loading a model
    if (class_exists('Category')) {
        echo "   ✓ Category model can be loaded\n";
    }
    
    if (class_exists('FAQ')) {
        echo "   ✓ FAQ model can be loaded\n";
    }
    
    // Test loading a controller
    if (class_exists('BaseController')) {
        echo "   ✓ BaseController can be loaded\n";
    }
} catch (Exception $e) {
    echo "   ✗ Autoloader failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n----------------------------\n";
echo "Bootstrap test completed!\n";
echo "\nIf database connection failed, make sure to:\n";
echo "1. Create the database: CREATE DATABASE <database_name>;\n";
echo "2. Import schema: mysql -u <username> -p <database_name> < database.sql\n";
echo "3. Update credentials in config/config.php if needed\n";
echo "\nCheck config/config.php for current database settings.\n";
