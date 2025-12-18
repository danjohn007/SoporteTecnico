<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#10B981',
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="<?php echo url(); ?>" class="text-2xl font-bold text-primary">
                            <i class="fas fa-headset mr-2"></i>
                            <?php echo SITE_NAME; ?>
                        </a>
                    </div>
                    <?php if (isLoggedIn()): ?>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="<?php echo url('dashboard'); ?>" class="border-transparent text-gray-500 hover:border-primary hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-home mr-2"></i> Dashboard
                        </a>
                        <a href="<?php echo url('tickets'); ?>" class="border-transparent text-gray-500 hover:border-primary hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-ticket-alt mr-2"></i> Mis Tickets
                        </a>
                        <a href="<?php echo url('faq'); ?>" class="border-transparent text-gray-500 hover:border-primary hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-question-circle mr-2"></i> FAQ
                        </a>
                        <a href="<?php echo url('chatbot'); ?>" class="border-transparent text-gray-500 hover:border-primary hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-robot mr-2"></i> Chatbot
                        </a>
                        <?php if (isAgent()): ?>
                        <a href="<?php echo url('agent'); ?>" class="border-transparent text-orange-500 hover:border-orange-500 hover:text-orange-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-user-headset mr-2"></i> Panel Agente
                        </a>
                        <?php endif; ?>
                        <?php if (isAdmin()): ?>
                        <a href="<?php echo url('admin'); ?>" class="border-transparent text-red-500 hover:border-red-500 hover:text-red-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-cog mr-2"></i> Administración
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="flex items-center">
                    <?php if (isLoggedIn()): ?>
                        <?php $user = getCurrentUser(); ?>
                        <div class="ml-3 relative flex items-center space-x-4">
                            <a href="<?php echo url('notifications'); ?>" class="text-gray-500 hover:text-gray-700 relative">
                                <i class="fas fa-bell text-xl"></i>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">3</span>
                            </a>
                            <div class="text-sm">
                                <div class="font-medium text-gray-700"><?php echo e($user['nombre_completo']); ?></div>
                                <div class="text-xs text-gray-500"><?php echo e($user['role']); ?></div>
                            </div>
                            <a href="<?php echo url('auth/logout'); ?>" class="text-gray-500 hover:text-red-600">
                                <i class="fas fa-sign-out-alt text-xl"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo url('auth/login'); ?>" class="text-gray-700 hover:text-primary px-3 py-2 rounded-md text-sm font-medium">
                            Iniciar Sesión
                        </a>
                        <a href="<?php echo url('auth/register'); ?>" class="ml-3 bg-primary text-white hover:bg-blue-600 px-4 py-2 rounded-md text-sm font-medium">
                            Registrarse
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-green-50 border-l-4 border-green-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['errors'])): ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-red-50 border-l-4 border-red-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <p class="text-sm text-red-700"><?php echo $error; ?></p>
                    <?php endforeach; ?>
                    <?php unset($_SESSION['errors']); ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php echo $content ?? ''; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Sistema de Soporte</h3>
                    <p class="text-gray-600 text-sm">
                        Plataforma integral de soporte técnico con gestión de tickets y chatbot inteligente.
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Enlaces Rápidos</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="<?php echo url('faq'); ?>" class="text-gray-600 hover:text-primary">Preguntas Frecuentes</a></li>
                        <li><a href="<?php echo url('home/contact'); ?>" class="text-gray-600 hover:text-primary">Contacto</a></li>
                        <li><a href="<?php echo url('home/about'); ?>" class="text-gray-600 hover:text-primary">Acerca de</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contacto</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><i class="fas fa-phone mr-2"></i> +52 442 123 4567</li>
                        <li><i class="fas fa-envelope mr-2"></i> soporte@queretaro.com</li>
                        <li><i class="fab fa-whatsapp mr-2"></i> WhatsApp Business</li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-200 text-center text-sm text-gray-500">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Sistema v<?php echo SYSTEM_VERSION; ?></p>
            </div>
        </div>
    </footer>

    <!-- Alpine.js for interactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
