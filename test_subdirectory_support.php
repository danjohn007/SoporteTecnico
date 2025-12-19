<?php
/**
 * Test Script - Subdirectory Installation Support
 * This script tests that BASE_URL detection works correctly in various installation scenarios
 */

// Load configuration
require_once __DIR__ . '/config/config.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Soporte para Subdirectorios - Sistema de Soporte Técnico</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8 bg-white p-8 rounded-lg shadow-lg">
            <div>
                <h1 class="text-center text-3xl font-extrabold text-gray-900">
                    Test de Soporte para Subdirectorios
                </h1>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Sistema de Soporte Técnico - Versión 1.0.2
                </p>
            </div>

            <div class="mt-8 space-y-6">
                <!-- Current Configuration -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h2 class="text-lg font-semibold text-blue-900 mb-3">
                        📍 Configuración Actual
                    </h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex">
                            <span class="font-medium text-blue-700 w-40">BASE_URL:</span>
                            <span class="text-blue-900"><?php echo htmlspecialchars(BASE_URL); ?></span>
                        </div>
                        <div class="flex">
                            <span class="font-medium text-blue-700 w-40">SCRIPT_NAME:</span>
                            <span class="text-blue-900"><?php echo htmlspecialchars($_SERVER['SCRIPT_NAME']); ?></span>
                        </div>
                        <div class="flex">
                            <span class="font-medium text-blue-700 w-40">REQUEST_URI:</span>
                            <span class="text-blue-900"><?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?></span>
                        </div>
                        <div class="flex">
                            <span class="font-medium text-blue-700 w-40">HTTP_HOST:</span>
                            <span class="text-blue-900"><?php echo htmlspecialchars($_SERVER['HTTP_HOST']); ?></span>
                        </div>
                        <div class="flex">
                            <span class="font-medium text-blue-700 w-40">HTTPS:</span>
                            <span class="text-blue-900"><?php echo isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'Sí' : 'No'; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Detected Installation Type -->
                <?php
                $installationType = 'Raíz del servidor';
                $installationPath = '/';
                $directory = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
                if ($directory !== '/' && $directory !== '') {
                    $installationType = 'Subdirectorio';
                    $installationPath = $directory;
                }
                ?>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h2 class="text-lg font-semibold text-green-900 mb-3">
                        ✅ Tipo de Instalación Detectado
                    </h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex">
                            <span class="font-medium text-green-700 w-40">Tipo:</span>
                            <span class="text-green-900"><?php echo $installationType; ?></span>
                        </div>
                        <div class="flex">
                            <span class="font-medium text-green-700 w-40">Ruta:</span>
                            <span class="text-green-900"><?php echo $installationPath; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Test URLs -->
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <h2 class="text-lg font-semibold text-purple-900 mb-3">
                        🔗 URLs Generadas por el Sistema
                    </h2>
                    <div class="space-y-2 text-sm">
                        <?php
                        $testUrls = [
                            'Página principal' => '',
                            'Login' => 'auth/login',
                            'Registro' => 'auth/register',
                            'Dashboard' => 'dashboard',
                            'Crear Ticket' => 'tickets/create',
                            'FAQs' => 'faq',
                            'Chatbot' => 'chatbot',
                        ];
                        
                        foreach ($testUrls as $label => $path) {
                            $fullUrl = BASE_URL . ($path ? '/' . $path : '');
                            echo '<div class="flex items-start">';
                            echo '<span class="font-medium text-purple-700 w-40">' . htmlspecialchars($label) . ':</span>';
                            echo '<a href="' . htmlspecialchars($fullUrl) . '" class="text-purple-900 hover:text-purple-600 underline break-all">' . htmlspecialchars($fullUrl) . '</a>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Information -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h2 class="text-lg font-semibold text-yellow-900 mb-3">
                        ℹ️ Información
                    </h2>
                    <div class="text-sm text-yellow-800 space-y-2">
                        <p>
                            <strong>Soporte para Subdirectorios:</strong> Este sistema ahora detecta automáticamente 
                            en qué directorio está instalado y ajusta todas las URLs en consecuencia.
                        </p>
                        <p>
                            <strong>No requiere configuración manual.</strong> El archivo <code class="bg-yellow-100 px-1 rounded">.htaccess</code> 
                            ya no tiene <code class="bg-yellow-100 px-1 rounded">RewriteBase</code> hardcodeado.
                        </p>
                        <p class="pt-2">
                            <strong>Escenarios soportados:</strong>
                        </p>
                        <ul class="list-disc ml-6 space-y-1">
                            <li>Instalación en raíz: <code class="bg-yellow-100 px-1 rounded">http://localhost/</code></li>
                            <li>Subdirectorio: <code class="bg-yellow-100 px-1 rounded">http://localhost/SoporteTecnico/</code></li>
                            <li>Subdirectorio profundo: <code class="bg-yellow-100 px-1 rounded">http://localhost/proyectos/soporte/</code></li>
                            <li>Con HTTPS: <code class="bg-yellow-100 px-1 rounded">https://ejemplo.com/SoporteTecnico/</code></li>
                        </ul>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <a href="<?php echo BASE_URL; ?>" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Ir a Página Principal
                    </a>
                    <a href="<?php echo BASE_URL; ?>/test_connection.php" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Test de Conexión
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
