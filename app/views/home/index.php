<?php ob_start(); ?>

<!-- Hero Section -->
<div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg shadow-xl p-8 md:p-12 mb-8">
    <div class="max-w-3xl">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">
            Soporte Técnico Inteligente
        </h1>
        <p class="text-xl md:text-2xl mb-6 text-blue-100">
            Sistema integral de tickets con chatbot FAQ para resolver tus dudas al instante
        </p>
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="<?php echo url('auth/register'); ?>" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition text-center">
                <i class="fas fa-user-plus mr-2"></i> Crear Cuenta
            </a>
            <a href="<?php echo url('chatbot'); ?>" class="bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-800 transition text-center">
                <i class="fas fa-robot mr-2"></i> Probar Chatbot
            </a>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="grid md:grid-cols-3 gap-8 mb-12">
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
        <div class="text-4xl text-blue-500 mb-4">
            <i class="fas fa-ticket-alt"></i>
        </div>
        <h3 class="text-xl font-semibold mb-2">Gestión de Tickets</h3>
        <p class="text-gray-600">
            Crea y administra tickets de soporte con diferentes prioridades y estados.
        </p>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
        <div class="text-4xl text-green-500 mb-4">
            <i class="fas fa-robot"></i>
        </div>
        <h3 class="text-xl font-semibold mb-2">Chatbot Inteligente</h3>
        <p class="text-gray-600">
            Obtén respuestas instantáneas basadas en nuestra base de conocimiento FAQ.
        </p>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
        <div class="text-4xl text-purple-500 mb-4">
            <i class="fab fa-whatsapp"></i>
        </div>
        <h3 class="text-xl font-semibold mb-2">Notificaciones WhatsApp</h3>
        <p class="text-gray-600">
            Recibe actualizaciones de tus tickets directamente en WhatsApp.
        </p>
    </div>
</div>

<!-- Popular FAQs -->
<?php if (!empty($popularFaqs)): ?>
<div class="bg-white rounded-lg shadow-md p-8 mb-8">
    <h2 class="text-2xl font-bold mb-6 flex items-center">
        <i class="fas fa-question-circle text-blue-500 mr-3"></i>
        Preguntas Frecuentes
    </h2>
    <div class="space-y-4">
        <?php foreach ($popularFaqs as $faq): ?>
        <div class="border-b border-gray-200 pb-4 last:border-0">
            <h3 class="font-semibold text-gray-900 mb-2">
                <i class="fas fa-chevron-right text-blue-500 text-sm mr-2"></i>
                <?php echo e($faq['pregunta']); ?>
            </h3>
            <p class="text-gray-600 text-sm pl-6">
                <?php echo e(substr($faq['respuesta'], 0, 150)) . '...'; ?>
            </p>
            <a href="<?php echo url('faq/view/' . $faq['id']); ?>" class="text-blue-500 text-sm pl-6 hover:underline">
                Ver más <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="mt-6 text-center">
        <a href="<?php echo url('faq'); ?>" class="inline-block bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">
            Ver todas las FAQs
        </a>
    </div>
</div>
<?php endif; ?>

<!-- How it Works -->
<div class="bg-white rounded-lg shadow-md p-8">
    <h2 class="text-2xl font-bold mb-6 text-center">¿Cómo Funciona?</h2>
    <div class="grid md:grid-cols-4 gap-6">
        <div class="text-center">
            <div class="bg-blue-100 text-blue-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                1
            </div>
            <h3 class="font-semibold mb-2">Regístrate</h3>
            <p class="text-sm text-gray-600">Solo necesitas tu nombre y WhatsApp</p>
        </div>
        
        <div class="text-center">
            <div class="bg-green-100 text-green-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                2
            </div>
            <h3 class="font-semibold mb-2">Busca en FAQ</h3>
            <p class="text-sm text-gray-600">Usa el chatbot para encontrar respuestas</p>
        </div>
        
        <div class="text-center">
            <div class="bg-purple-100 text-purple-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                3
            </div>
            <h3 class="font-semibold mb-2">Crea un Ticket</h3>
            <p class="text-sm text-gray-600">Si no encuentras solución, abre un ticket</p>
        </div>
        
        <div class="text-center">
            <div class="bg-orange-100 text-orange-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                4
            </div>
            <h3 class="font-semibold mb-2">Recibe Soporte</h3>
            <p class="text-sm text-gray-600">Un agente resolverá tu problema</p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
