<?php ob_start(); ?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                <i class="fas fa-ticket-alt text-2xl text-blue-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Total Tickets</p>
                <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['total_tickets'] ?? 0; ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                <i class="fas fa-clock text-2xl text-yellow-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Abiertos</p>
                <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['tickets_abiertos'] ?? 0; ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-orange-100 rounded-md p-3">
                <i class="fas fa-spinner text-2xl text-orange-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">En Proceso</p>
                <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['tickets_en_proceso'] ?? 0; ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                <i class="fas fa-check-circle text-2xl text-green-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Resueltos</p>
                <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['tickets_resueltos'] ?? 0; ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <h2 class="text-xl font-bold mb-4">Acciones Rápidas</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="<?php echo url('tickets/create'); ?>" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
            <i class="fas fa-plus-circle text-3xl text-blue-600 mr-4"></i>
            <div>
                <div class="font-semibold text-gray-900">Crear Ticket</div>
                <div class="text-sm text-gray-600">Nuevo ticket de soporte</div>
            </div>
        </a>
        
        <a href="<?php echo url('chatbot'); ?>" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
            <i class="fas fa-robot text-3xl text-green-600 mr-4"></i>
            <div>
                <div class="font-semibold text-gray-900">Chatbot</div>
                <div class="text-sm text-gray-600">Asistente virtual</div>
            </div>
        </a>
        
        <a href="<?php echo url('faq'); ?>" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
            <i class="fas fa-question-circle text-3xl text-purple-600 mr-4"></i>
            <div>
                <div class="font-semibold text-gray-900">FAQs</div>
                <div class="text-sm text-gray-600">Preguntas frecuentes</div>
            </div>
        </a>
    </div>
</div>

<!-- Recent Tickets -->
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold">Mis Tickets Recientes</h2>
            <a href="<?php echo url('tickets'); ?>" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                Ver todos <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
    
    <div class="divide-y divide-gray-200">
        <?php if (!empty($tickets)): ?>
            <?php foreach (array_slice($tickets, 0, 5) as $ticket): ?>
            <div class="p-6 hover:bg-gray-50 transition">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            <a href="<?php echo url('tickets/view/' . $ticket['id']); ?>" class="text-lg font-semibold text-gray-900 hover:text-blue-600">
                                <?php echo e($ticket['ticket_id']); ?>
                            </a>
                            <span class="ml-3 px-2 py-1 text-xs rounded-full <?php echo getStatusBadge($ticket['estado']); ?>">
                                <?php echo getStatusLabel($ticket['estado']); ?>
                            </span>
                            <span class="ml-2 px-2 py-1 text-xs rounded-full <?php echo getPriorityBadge($ticket['prioridad']); ?>">
                                <?php echo getPriorityLabel($ticket['prioridad']); ?>
                            </span>
                        </div>
                        <p class="text-gray-600 mb-2"><?php echo e($ticket['asunto']); ?></p>
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-folder mr-1"></i>
                            <span><?php echo e($ticket['categoria_nombre']); ?></span>
                            <span class="mx-2">•</span>
                            <i class="fas fa-clock mr-1"></i>
                            <span><?php echo timeAgo($ticket['created_at']); ?></span>
                        </div>
                    </div>
                    <div>
                        <a href="<?php echo url('tickets/view/' . $ticket['id']); ?>" class="text-blue-600 hover:text-blue-700">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-inbox text-5xl mb-4"></i>
                <p class="text-lg">No tienes tickets aún</p>
                <a href="<?php echo url('tickets/create'); ?>" class="inline-block mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Crear tu primer ticket
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
