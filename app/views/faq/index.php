<?php ob_start(); ?>

<div class="mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold mb-2">
                <i class="fas fa-question-circle text-purple-500 mr-2"></i>
                Preguntas Frecuentes
            </h1>
            <p class="text-gray-600">Encuentra respuestas rápidas a las preguntas más comunes</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="<?php echo url('chatbot'); ?>" class="inline-block bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                <i class="fas fa-robot mr-2"></i> Usar Chatbot
            </a>
        </div>
    </div>
</div>

<!-- Search Box -->
<div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <form action="<?php echo url('faq'); ?>" method="GET">
        <div class="flex gap-2">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input 
                    type="text" 
                    name="search"
                    placeholder="Buscar en FAQs..."
                    value="<?php echo e($searchQuery ?? ''); ?>"
                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                >
            </div>
            <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                Buscar
            </button>
        </div>
    </form>
</div>

<!-- Categories Filter -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <div class="flex flex-wrap gap-2">
        <a href="<?php echo url('faq'); ?>" class="px-4 py-2 rounded-lg <?php echo !$currentCategory ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
            <i class="fas fa-th mr-1"></i> Todas
        </a>
        <?php foreach ($categories as $category): ?>
        <a href="<?php echo url('faq?category=' . $category['id']); ?>" class="px-4 py-2 rounded-lg <?php echo $currentCategory == $category['id'] ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
            <i class="fas fa-folder mr-1"></i> <?php echo e($category['nombre']); ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- FAQs List -->
<?php if (!empty($faqs)): ?>
    <div class="space-y-4">
        <?php foreach ($faqs as $faq): ?>
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            <h3 class="text-xl font-semibold text-gray-900">
                                <i class="fas fa-question-circle text-purple-500 mr-2"></i>
                                <?php echo e($faq['pregunta']); ?>
                            </h3>
                        </div>
                        
                        <div class="text-gray-700 mb-4 leading-relaxed">
                            <?php echo nl2br(e($faq['respuesta'])); ?>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                <?php if ($faq['categoria_nombre']): ?>
                                <span>
                                    <i class="fas fa-folder mr-1"></i>
                                    <?php echo e($faq['categoria_nombre']); ?>
                                </span>
                                <?php endif; ?>
                                <span>
                                    <i class="fas fa-eye mr-1"></i>
                                    <?php echo $faq['views']; ?> vistas
                                </span>
                                <span class="text-green-600">
                                    <i class="fas fa-thumbs-up mr-1"></i>
                                    <?php echo $faq['helpful_count']; ?>
                                </span>
                            </div>
                            
                            <a href="<?php echo url('faq/view/' . $faq['id']); ?>" class="text-purple-600 hover:text-purple-700 font-medium">
                                Ver más <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No se encontraron resultados</h3>
        <p class="text-gray-600 mb-6">
            Intenta con otros términos de búsqueda o explora todas las categorías
        </p>
        <div class="flex justify-center gap-4">
            <a href="<?php echo url('faq'); ?>" class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700">
                Ver Todas las FAQs
            </a>
            <a href="<?php echo url('chatbot'); ?>" class="inline-block bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                Usar Chatbot
            </a>
        </div>
    </div>
<?php endif; ?>

<!-- Help Section -->
<div class="mt-8 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-8 text-white">
    <div class="flex flex-col md:flex-row items-center justify-between">
        <div class="mb-4 md:mb-0">
            <h2 class="text-2xl font-bold mb-2">¿No encontraste lo que buscabas?</h2>
            <p class="text-purple-100">Nuestro equipo de soporte está listo para ayudarte</p>
        </div>
        <a href="<?php echo url('tickets/create'); ?>" class="bg-white text-purple-600 px-8 py-3 rounded-lg font-semibold hover:bg-purple-50 transition">
            Crear Ticket de Soporte
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
