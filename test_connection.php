<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Conexión - Sistema de Soporte Técnico</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-lg">
            <div>
                <h1 class="text-center text-3xl font-extrabold text-gray-900">
                    Test de Conexión
                </h1>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Sistema de Soporte Técnico
                </p>
            </div>

            <div class="mt-8 space-y-4">
                <?php
                // Load configuration
                require_once __DIR__ . '/config/config.php';
                require_once __DIR__ . '/config/database.php';

                $tests = [];
                
                // Test 1: PHP Version
                $phpVersion = phpversion();
                $tests[] = [
                    'name' => 'Versión de PHP',
                    'status' => version_compare($phpVersion, '7.0.0', '>='),
                    'message' => "PHP $phpVersion " . (version_compare($phpVersion, '7.0.0', '>=') ? '✓' : '✗ (Se requiere PHP 7.0+)')
                ];
                
                // Test 2: Required Extensions
                $extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
                foreach ($extensions as $ext) {
                    $loaded = extension_loaded($ext);
                    $tests[] = [
                        'name' => "Extensión: $ext",
                        'status' => $loaded,
                        'message' => $ext . ' ' . ($loaded ? '✓' : '✗')
                    ];
                }
                
                // Test 3: BASE_URL Detection
                $tests[] = [
                    'name' => 'URL Base',
                    'status' => true,
                    'message' => BASE_URL
                ];
                
                // Test 4: Database Connection
                $dbConnected = false;
                $dbMessage = '';
                try {
                    $db = Database::getInstance()->getConnection();
                    $dbConnected = true;
                    $dbMessage = 'Conexión exitosa a: ' . DB_NAME;
                } catch (Exception $e) {
                    $dbMessage = 'Error: ' . $e->getMessage();
                }
                
                $tests[] = [
                    'name' => 'Conexión a Base de Datos',
                    'status' => $dbConnected,
                    'message' => $dbMessage
                ];
                
                // Test 5: Check if tables exist
                if ($dbConnected) {
                    try {
                        $stmt = $db->query("SHOW TABLES");
                        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                        $requiredTables = ['users', 'tickets', 'categories', 'faqs', 'system_settings'];
                        $tablesExist = count(array_intersect($requiredTables, $tables)) === count($requiredTables);
                        
                        $tests[] = [
                            'name' => 'Tablas de Base de Datos',
                            'status' => $tablesExist,
                            'message' => $tablesExist ? 
                                'Todas las tablas necesarias existen (' . count($tables) . ' tablas encontradas)' : 
                                'Algunas tablas faltan. Por favor ejecuta database.sql'
                        ];
                    } catch (Exception $e) {
                        $tests[] = [
                            'name' => 'Tablas de Base de Datos',
                            'status' => false,
                            'message' => 'Error al verificar tablas: ' . $e->getMessage()
                        ];
                    }
                }
                
                // Test 6: File Permissions
                $uploadPath = __DIR__ . '/public/uploads/';
                $writable = is_writable($uploadPath);
                $tests[] = [
                    'name' => 'Permisos de Escritura (uploads)',
                    'status' => $writable,
                    'message' => $writable ? 
                        'Directorio /public/uploads/ es escribible ✓' : 
                        'Directorio /public/uploads/ no es escribible ✗'
                ];
                
                // Test 7: .htaccess
                $htaccessExists = file_exists(__DIR__ . '/.htaccess');
                $tests[] = [
                    'name' => 'Archivo .htaccess',
                    'status' => $htaccessExists,
                    'message' => $htaccessExists ? '.htaccess encontrado ✓' : '.htaccess no encontrado ✗'
                ];
                
                // Display results
                $allPassed = true;
                foreach ($tests as $test) {
                    if (!$test['status']) {
                        $allPassed = false;
                    }
                    
                    $bgColor = $test['status'] ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200';
                    $textColor = $test['status'] ? 'text-green-800' : 'text-red-800';
                    $icon = $test['status'] ? 
                        '<svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>' :
                        '<svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>';
                    
                    echo "
                    <div class='border $bgColor rounded-lg p-4'>
                        <div class='flex items-start'>
                            <div class='flex-shrink-0'>
                                $icon
                            </div>
                            <div class='ml-3 flex-1'>
                                <h3 class='text-sm font-medium $textColor'>
                                    {$test['name']}
                                </h3>
                                <div class='mt-1 text-sm $textColor'>
                                    {$test['message']}
                                </div>
                            </div>
                        </div>
                    </div>
                    ";
                }
                ?>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <?php if ($allPassed): ?>
                        <div class="rounded-md bg-green-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-green-800">
                                        ¡Sistema listo!
                                    </h3>
                                    <div class="mt-2 text-sm text-green-700">
                                        <p>Todos los tests pasaron correctamente. El sistema está listo para usarse.</p>
                                    </div>
                                    <div class="mt-4">
                                        <a href="<?php echo BASE_URL; ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            Ir al sistema
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="rounded-md bg-yellow-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        Configuración incompleta
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Por favor corrige los errores mostrados arriba antes de usar el sistema.</p>
                                        <ul class="list-disc list-inside mt-2">
                                            <li>Verifica la configuración en config/config.php</li>
                                            <li>Asegúrate de ejecutar database.sql en MySQL</li>
                                            <li>Verifica los permisos de archivos y directorios</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-6 text-center text-xs text-gray-500">
                    <p>Sistema de Soporte Técnico v<?php echo SYSTEM_VERSION; ?></p>
                    <p class="mt-1">Generado: <?php echo date('Y-m-d H:i:s'); ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
