<?php ob_start(); ?>

<!-- Ticket Header -->
<div class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between mb-4">
        <div class="flex-1">
            <div class="flex items-center mb-3">
                <h1 class="text-3xl font-bold text-gray-900">
                    <?php echo e($ticket['ticket_id']); ?>
                </h1>
                <span class="ml-3 px-3 py-1 text-sm rounded-full <?php echo getStatusBadge($ticket['estado']); ?>">
                    <i class="fas fa-circle text-xs mr-1"></i>
                    <?php echo getStatusLabel($ticket['estado']); ?>
                </span>
                <span class="ml-2 px-3 py-1 text-sm rounded-full <?php echo getPriorityBadge($ticket['prioridad']); ?>">
                    <i class="fas fa-flag mr-1"></i>
                    <?php echo getPriorityLabel($ticket['prioridad']); ?>
                </span>
            </div>
            
            <h2 class="text-xl text-gray-900 mb-3"><?php echo e($ticket['asunto']); ?></h2>
            
            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                <span>
                    <i class="fas fa-user mr-1"></i>
                    <strong>Cliente:</strong> <?php echo e($ticket['usuario_nombre']); ?>
                </span>
                <span>
                    <i class="fas fa-folder mr-1"></i>
                    <strong>Categoría:</strong> <?php echo e($ticket['categoria_nombre']); ?>
                </span>
                <span>
                    <i class="fas fa-calendar mr-1"></i>
                    <strong>Creado:</strong> <?php echo formatDate($ticket['created_at']); ?>
                </span>
                <?php if ($ticket['agente_nombre']): ?>
                <span>
                    <i class="fas fa-user-headset mr-1"></i>
                    <strong>Agente:</strong> <?php echo e($ticket['agente_nombre']); ?>
                </span>
                <?php else: ?>
                <span class="text-orange-600">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <strong>Sin asignar</strong>
                </span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mt-4 md:mt-0 flex flex-col space-y-2">
            <?php if (isAgent()): ?>
                <!-- Agent Actions -->
                <?php if (!$ticket['agent_id']): ?>
                <form action="<?php echo url('tickets/assign/' . $ticket['id']); ?>" method="POST">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-hand-paper mr-2"></i> Asignarme
                    </button>
                </form>
                <?php endif; ?>
                
                <!-- Change Status -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        <i class="fas fa-exchange-alt mr-2"></i> Cambiar Estado
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg z-10">
                        <form action="<?php echo url('tickets/changeStatus/' . $ticket['id']); ?>" method="POST">
                            <?php 
                            $statuses = [
                                'abierto' => 'Abierto',
                                'en_proceso' => 'En Proceso',
                                'en_espera_cliente' => 'En Espera de Cliente',
                                'resuelto' => 'Resuelto',
                                'cerrado' => 'Cerrado'
                            ];
                            foreach ($statuses as $key => $label):
                                if ($key !== $ticket['estado']):
                            ?>
                            <button type="submit" name="status" value="<?php echo $key; ?>" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                                <span class="px-2 py-1 text-xs rounded-full <?php echo getStatusBadge($key); ?>">
                                    <?php echo $label; ?>
                                </span>
                            </button>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
            
            <a href="<?php echo url('tickets'); ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-center">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
    </div>
</div>

<!-- Conversation Thread -->
<div class="bg-white rounded-lg shadow-lg mb-6">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-xl font-bold flex items-center">
            <i class="fas fa-comments text-blue-500 mr-2"></i>
            Conversación
        </h3>
    </div>
    
    <div class="p-6 space-y-6">
        <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $message): ?>
            <div class="flex items-start <?php echo $message['role'] !== 'user' ? '' : 'flex-row-reverse'; ?>">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white <?php echo $message['role'] === 'user' ? 'bg-blue-500' : ($message['role'] === 'agent' ? 'bg-green-500' : 'bg-purple-500'); ?>">
                        <i class="fas <?php echo $message['role'] === 'user' ? 'fa-user' : 'fa-user-headset'; ?>"></i>
                    </div>
                </div>
                <div class="<?php echo $message['role'] !== 'user' ? 'ml-4' : 'mr-4'; ?> flex-1">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-900"><?php echo e($message['nombre_completo']); ?></span>
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-clock mr-1"></i>
                                <?php echo timeAgo($message['created_at']); ?>
                            </span>
                        </div>
                        <p class="text-gray-700 whitespace-pre-wrap"><?php echo e($message['mensaje']); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Attachments -->
<?php if (!empty($attachments)): ?>
<div class="bg-white rounded-lg shadow-lg mb-6">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-xl font-bold flex items-center">
            <i class="fas fa-paperclip text-gray-500 mr-2"></i>
            Archivos Adjuntos
        </h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php foreach ($attachments as $attachment): ?>
            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 transition">
                <div class="flex items-center">
                    <i class="fas fa-file text-3xl text-gray-400 mr-3"></i>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            <?php echo e($attachment['original_filename']); ?>
                        </p>
                        <p class="text-xs text-gray-500">
                            <?php echo round($attachment['file_size'] / 1024, 2); ?> KB
                        </p>
                    </div>
                </div>
                <a href="<?php echo url('public/uploads/' . $attachment['file_path']); ?>" target="_blank" class="mt-3 block w-full text-center px-3 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm">
                    <i class="fas fa-download mr-2"></i> Descargar
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Reply Form -->
<?php if ($ticket['estado'] !== 'cerrado'): ?>
<div class="bg-white rounded-lg shadow-lg">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-xl font-bold flex items-center">
            <i class="fas fa-reply text-blue-500 mr-2"></i>
            Responder
        </h3>
    </div>
    <div class="p-6">
        <form action="<?php echo url('tickets/reply/' . $ticket['id']); ?>" method="POST" enctype="multipart/form-data">
            <div class="space-y-4">
                <div>
                    <label for="mensaje" class="block text-sm font-medium text-gray-700 mb-2">
                        Tu Respuesta
                    </label>
                    <textarea 
                        name="mensaje" 
                        id="mensaje" 
                        rows="5"
                        required
                        class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Escribe tu mensaje aquí..."
                    ></textarea>
                </div>
                
                <div>
                    <label for="attachment" class="block text-sm font-medium text-gray-700 mb-2">
                        Adjuntar Archivo (Opcional)
                    </label>
                    <input 
                        type="file" 
                        name="attachment" 
                        id="attachment"
                        accept=".jpg,.jpeg,.png,.gif,.pdf,.txt"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                    >
                    <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF, PDF o TXT (máx. 5MB)</p>
                </div>
                
                <div class="flex justify-end">
                    <button 
                        type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        <i class="fas fa-paper-plane mr-2"></i>
                        Enviar Respuesta
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
    <i class="fas fa-lock text-3xl text-gray-400 mb-2"></i>
    <p class="text-gray-600">Este ticket está cerrado y no acepta más respuestas.</p>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
