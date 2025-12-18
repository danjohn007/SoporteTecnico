<?php ob_start(); ?>

<!-- Admin Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold mb-2">
        <i class="fas fa-cog text-red-500 mr-2"></i>
        Panel de Administración
    </h1>
    <p class="text-gray-600">Gestión completa del sistema</p>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm mb-1">Total Tickets</p>
                <p class="text-3xl font-bold"><?php echo $stats['total'] ?? 0; ?></p>
            </div>
            <i class="fas fa-ticket-alt text-5xl opacity-20"></i>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-yellow-100 text-sm mb-1">Abiertos</p>
                <p class="text-3xl font-bold"><?php echo $stats['abiertos'] ?? 0; ?></p>
            </div>
            <i class="fas fa-folder-open text-5xl opacity-20"></i>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm mb-1">En Proceso</p>
                <p class="text-3xl font-bold"><?php echo $stats['en_proceso'] ?? 0; ?></p>
            </div>
            <i class="fas fa-spinner text-5xl opacity-20"></i>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm mb-1">Resueltos</p>
                <p class="text-3xl font-bold"><?php echo $stats['resueltos'] ?? 0; ?></p>
            </div>
            <i class="fas fa-check-circle text-5xl opacity-20"></i>
        </div>
    </div>
</div>

<!-- Quick Admin Actions -->
<div class="grid md:grid-cols-4 gap-4 mb-8">
    <a href="<?php echo url('admin/users'); ?>" class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
        <i class="fas fa-users text-3xl text-blue-600 mb-3"></i>
        <h3 class="font-semibold text-gray-900">Usuarios</h3>
        <p class="text-sm text-gray-600">Gestionar usuarios</p>
    </a>
    
    <a href="<?php echo url('admin/categories'); ?>" class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
        <i class="fas fa-folder text-3xl text-purple-600 mb-3"></i>
        <h3 class="font-semibold text-gray-900">Categorías</h3>
        <p class="text-sm text-gray-600">Administrar categorías</p>
    </a>
    
    <a href="<?php echo url('admin/faqs'); ?>" class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
        <i class="fas fa-question-circle text-3xl text-green-600 mb-3"></i>
        <h3 class="font-semibold text-gray-900">FAQs</h3>
        <p class="text-sm text-gray-600">Gestionar FAQs</p>
    </a>
    
    <a href="<?php echo url('settings'); ?>" class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
        <i class="fas fa-cog text-3xl text-gray-600 mb-3"></i>
        <h3 class="font-semibold text-gray-900">Configuración</h3>
        <p class="text-sm text-gray-600">Ajustes del sistema</p>
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="estado" class="px-4 py-2 border border-gray-300 rounded-lg">
            <option value="">Todos los estados</option>
            <option value="abierto" <?php echo ($filters['estado'] ?? '') === 'abierto' ? 'selected' : ''; ?>>Abierto</option>
            <option value="en_proceso" <?php echo ($filters['estado'] ?? '') === 'en_proceso' ? 'selected' : ''; ?>>En Proceso</option>
            <option value="resuelto" <?php echo ($filters['estado'] ?? '') === 'resuelto' ? 'selected' : ''; ?>>Resuelto</option>
            <option value="cerrado" <?php echo ($filters['estado'] ?? '') === 'cerrado' ? 'selected' : ''; ?>>Cerrado</option>
        </select>
        
        <select name="prioridad" class="px-4 py-2 border border-gray-300 rounded-lg">
            <option value="">Todas las prioridades</option>
            <option value="baja" <?php echo ($filters['prioridad'] ?? '') === 'baja' ? 'selected' : ''; ?>>Baja</option>
            <option value="media" <?php echo ($filters['prioridad'] ?? '') === 'media' ? 'selected' : ''; ?>>Media</option>
            <option value="alta" <?php echo ($filters['prioridad'] ?? '') === 'alta' ? 'selected' : ''; ?>>Alta</option>
            <option value="critica" <?php echo ($filters['prioridad'] ?? '') === 'critica' ? 'selected' : ''; ?>>Crítica</option>
        </select>
        
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-filter mr-2"></i> Filtrar
        </button>
    </form>
</div>

<!-- Recent Tickets -->
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-bold">Tickets Recientes</h2>
    </div>
    
    <div class="divide-y divide-gray-200">
        <?php if (!empty($tickets)): ?>
            <?php foreach (array_slice($tickets, 0, 10) as $ticket): ?>
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
                        <p class="text-gray-900 mb-1"><?php echo e($ticket['asunto']); ?></p>
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            <span>
                                <i class="fas fa-user mr-1"></i>
                                <?php echo e($ticket['usuario_nombre']); ?>
                            </span>
                            <span>
                                <i class="fas fa-folder mr-1"></i>
                                <?php echo e($ticket['categoria_nombre']); ?>
                            </span>
                            <span>
                                <i class="fas fa-clock mr-1"></i>
                                <?php echo timeAgo($ticket['created_at']); ?>
                            </span>
                            <?php if ($ticket['agente_nombre']): ?>
                            <span>
                                <i class="fas fa-user-headset mr-1"></i>
                                <?php echo e($ticket['agente_nombre']); ?>
                            </span>
                            <?php else: ?>
                            <span class="text-orange-500">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Sin asignar
                            </span>
                            <?php endif; ?>
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
                <p class="text-lg">No hay tickets</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
