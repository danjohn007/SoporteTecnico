<?php ob_start(); ?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="text-center">
                <i class="fas fa-user-plus text-6xl text-blue-500 mb-4"></i>
            </div>
            <h2 class="text-center text-3xl font-extrabold text-gray-900">
                Crear Cuenta
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Solo necesitas tu nombre y WhatsApp. <br>
                ¿Ya tienes cuenta? 
                <a href="<?php echo url('auth/login'); ?>" class="font-medium text-blue-600 hover:text-blue-500">
                    Inicia sesión aquí
                </a>
            </p>
        </div>
        
        <div class="bg-white shadow-xl rounded-lg p-8">
            <form class="space-y-6" action="<?php echo url('auth/doRegister'); ?>" method="POST">
                <div>
                    <label for="nombre_completo" class="block text-sm font-medium text-gray-700">
                        Nombre Completo
                    </label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input 
                            id="nombre_completo" 
                            name="nombre_completo" 
                            type="text" 
                            required 
                            class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Juan Pérez García"
                            value="<?php echo $_SESSION['old_input']['nombre_completo'] ?? ''; ?>"
                        >
                    </div>
                </div>

                <div>
                    <label for="whatsapp" class="block text-sm font-medium text-gray-700">
                        Número de WhatsApp
                    </label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fab fa-whatsapp text-gray-400"></i>
                        </div>
                        <input 
                            id="whatsapp" 
                            name="whatsapp" 
                            type="tel" 
                            required 
                            class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="+52 442 123 4567"
                            value="<?php echo $_SESSION['old_input']['whatsapp'] ?? ''; ?>"
                        >
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Formato: +52 442 123 4567 (LADA + número)
                    </p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Tu número de WhatsApp será tu identificador único. No se requiere contraseña.
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <button 
                        type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-user-plus text-blue-500 group-hover:text-blue-400"></i>
                        </span>
                        Crear Cuenta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
unset($_SESSION['old_input']);
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
