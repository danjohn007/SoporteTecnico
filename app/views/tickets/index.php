<?php ob_start(); ?>

<div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-bold">
        <i class="fas fa-ticket-alt text-blue-500 mr-2"></i>
        Mis Tickets
    </h1>
    <a href="<?php echo url('tickets/create'); ?>" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
        <i class="fas fa-plus mr-2"></i> Nuevo Ticket
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <div class="flex flex-wrap gap-2">
        <a href="<?php echo url('tickets'); ?>" class="px-4 py-2 rounded-lg <?php echo !$currentFilter ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
            Todos
        </a>
        <a href="<?php echo url('tickets?estado=abierto'); ?>" class="px-4 py-2 rounded-lg <?php echo $currentFilter === 'abierto' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
            Abiertos
        </a>
        <a href="<?php echo url('tickets?estado=en_proceso'); ?>" class="px-4 py-2 rounded-lg <?php echo $currentFilter === 'en_proceso' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
            En Proceso
        </a>
        <a href="<?php echo url('tickets?estado=en_espera_cliente'); ?>" class="px-4 py-2 rounded-lg <?php echo $currentFilter === 'en_espera_cliente' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
            En Espera
        </a>
        <a href="<?php echo url('tickets?estado=resuelto'); ?>" class="px-4 py-2 rounded-lg <?php echo $currentFilter === 'resuelto' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
            Resueltos
        </a>
        <a href="<?php echo url('tickets?estado=cerrado'); ?>" class="px-4 py-2 rounded-lg <?php echo $currentFilter === 'cerrado' ? 'bg-gray-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
            Cerrados
        </a>
    </div>
</div>

<!-- Tickets List -->
<div class="space-y-4">
    <?php if (!empty($tickets)): ?>
        <?php foreach ($tickets as $ticket): ?>
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-3">
                        <a href="<?php echo url('tickets/view/' . $ticket['id']); ?>" class="text-xl font-semibold text-gray-900 hover:text-blue-600">
                            <?php echo e($ticket['ticket_id']); ?>
                        </a>
                        <span class="ml-3 px-3 py-1 text-sm rounded-full <?php echo getStatusBadge($ticket['estado']); ?>">
                            <?php echo getStatusLabel($ticket['estado']); ?>
                        </span>
                        <span class="ml-2 px-3 py-1 text-sm rounded-full <?php echo getPriorityBadge($ticket['prioridad']); ?>">
                            <i class="fas fa-flag mr-1"></i>
                            <?php echo getPriorityLabel($ticket['prioridad']); ?>
                        </span>
                    </div>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        <?php echo e($ticket['asunto']); ?>
                    </h3>
                    
                    <p class="text-gray-600 mb-3 line-clamp-2">
                        <?php echo e(substr($ticket['descripcion'], 0, 200)) . '...'; ?>
                    </p>
                    
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                        <span>
                            <i class="fas fa-folder mr-1"></i>
                            <?php echo e($ticket['categoria_nombre']); ?>
                        </span>
                        <span>
                            <i class="fas fa-calendar mr-1"></i>
                            <?php echo formatDate($ticket['created_at']); ?>
                        </span>
                        <span>
                            <i class="fas fa-clock mr-1"></i>
                            <?php echo timeAgo($ticket['last_activity']); ?>
                        </span>
                        <?php if ($ticket['agente_nombre']): ?>
                        <span>
                            <i class="fas fa-user-headset mr-1"></i>
                            <?php echo e($ticket['agente_nombre']); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="ml-4">
                    <a href="<?php echo url('tickets/view/' . $ticket['id']); ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Ver Detalles
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No hay tickets</h3>
            <p class="text-gray-600 mb-6">
                <?php if ($currentFilter): ?>
                    No tienes tickets con el estado "<?php echo getStatusLabel($currentFilter); ?>"
                <?php else: ?>
                    Aún no has creado ningún ticket
                <?php endif; ?>
            </p>
            <a href="<?php echo url('tickets/create'); ?>" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i> Crear Primer Ticket
            </a>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
