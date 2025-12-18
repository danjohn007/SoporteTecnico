<?php ob_start(); ?>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold mb-6">
            <i class="fas fa-plus-circle text-blue-500 mr-2"></i>
            Crear Nuevo Ticket
        </h1>
        
        <form action="<?php echo url('tickets/store'); ?>" method="POST" enctype="multipart/form-data">
            <div class="space-y-6">
                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Categoría <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="category_id" 
                        id="category_id" 
                        required
                        class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">Selecciona una categoría</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>">
                            <?php echo e($category['nombre']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Priority -->
                <div>
                    <label for="prioridad" class="block text-sm font-medium text-gray-700 mb-2">
                        Prioridad <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="prioridad" 
                        id="prioridad" 
                        required
                        class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="baja">Baja</option>
                        <option value="media" selected>Media</option>
                        <option value="alta">Alta</option>
                        <option value="critica">Crítica</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">
                        <span class="font-medium">Baja:</span> Consulta general (48h) | 
                        <span class="font-medium">Media:</span> Problema menor (24h) | 
                        <span class="font-medium">Alta:</span> Problema importante (4h) | 
                        <span class="font-medium">Crítica:</span> Servicio no funciona (1h)
                    </p>
                </div>
                
                <!-- Subject -->
                <div>
                    <label for="asunto" class="block text-sm font-medium text-gray-700 mb-2">
                        Asunto <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="asunto" 
                        id="asunto" 
                        required
                        maxlength="255"
                        class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ej: No puedo acceder a mi cuenta"
                    >
                </div>
                
                <!-- Description -->
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción Detallada <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        name="descripcion" 
                        id="descripcion" 
                        rows="6"
                        required
                        class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Describe tu problema con el mayor detalle posible..."
                    ></textarea>
                    <p class="mt-1 text-xs text-gray-500">
                        Proporciona toda la información relevante: qué estabas haciendo, qué mensaje de error aparece, etc.
                    </p>
                </div>
                
                <!-- File Attachment -->
                <div>
                    <label for="attachment" class="block text-sm font-medium text-gray-700 mb-2">
                        Adjuntar Archivo (Opcional)
                    </label>
                    <div class="flex items-center">
                        <label class="w-full flex flex-col items-center px-4 py-6 bg-white text-gray-500 rounded-lg border-2 border-dashed border-gray-300 cursor-pointer hover:bg-gray-50">
                            <i class="fas fa-cloud-upload-alt text-3xl mb-2"></i>
                            <span class="text-sm">Haz clic para seleccionar un archivo</span>
                            <span class="text-xs mt-1">JPG, PNG, GIF, PDF o TXT (máx. 5MB)</span>
                            <input type="file" name="attachment" id="attachment" class="hidden" accept=".jpg,.jpeg,.png,.gif,.pdf,.txt">
                        </label>
                    </div>
                    <div id="file-name" class="mt-2 text-sm text-gray-600"></div>
                </div>
                
                <!-- Info Box -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3 text-sm text-blue-700">
                            <p class="font-medium mb-1">Antes de crear un ticket:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Revisa las <a href="<?php echo url('faq'); ?>" class="underline font-medium">Preguntas Frecuentes</a></li>
                                <li>Usa nuestro <a href="<?php echo url('chatbot'); ?>" class="underline font-medium">Chatbot</a> para respuestas inmediatas</li>
                                <li>Recibirás notificaciones por WhatsApp sobre el progreso</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="<?php echo url('dashboard'); ?>" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button 
                        type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        <i class="fas fa-paper-plane mr-2"></i>
                        Crear Ticket
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('attachment').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    const fileNameDiv = document.getElementById('file-name');
    if (fileName) {
        fileNameDiv.innerHTML = '<i class="fas fa-file mr-2"></i> ' + fileName;
    } else {
        fileNameDiv.innerHTML = '';
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
