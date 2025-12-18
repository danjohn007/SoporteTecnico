<?php ob_start(); ?>

<div class="mb-8">
    <h1 class="text-3xl font-bold mb-2">
        <i class="fas fa-cog text-blue-500 mr-2"></i>
        Configuraciones del Sistema
    </h1>
    <p class="text-gray-600">Personaliza y configura el comportamiento del sistema</p>
</div>

<form action="<?php echo url('settings/save'); ?>" method="POST" enctype="multipart/form-data">
    <div class="space-y-6">
        
        <!-- General Settings -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold flex items-center">
                    <i class="fas fa-home text-blue-500 mr-2"></i>
                    Configuración General
                </h2>
            </div>
            <div class="p-6 space-y-4">
                <?php foreach ($grouped['general'] as $setting): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo ucfirst(str_replace('_', ' ', $setting['setting_key'])); ?>
                    </label>
                    <?php if ($setting['setting_type'] === 'file'): ?>
                        <input 
                            type="file" 
                            name="site_logo"
                            accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        >
                        <?php if ($setting['setting_value']): ?>
                        <p class="text-xs text-gray-500 mt-1">Logo actual: <?php echo e($setting['setting_value']); ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <input 
                            type="text" 
                            name="settings[<?php echo $setting['setting_key']; ?>]"
                            value="<?php echo e($setting['setting_value']); ?>"
                            class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        >
                    <?php endif; ?>
                    <?php if ($setting['description']): ?>
                    <p class="text-xs text-gray-500 mt-1"><?php echo e($setting['description']); ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Contact Settings -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold flex items-center">
                    <i class="fas fa-phone text-green-500 mr-2"></i>
                    Información de Contacto
                </h2>
            </div>
            <div class="p-6 space-y-4">
                <?php foreach ($grouped['contact'] as $setting): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo ucfirst(str_replace('_', ' ', $setting['setting_key'])); ?>
                    </label>
                    <?php if ($setting['setting_type'] === 'json'): ?>
                        <textarea 
                            name="settings[<?php echo $setting['setting_key']; ?>]"
                            rows="6"
                            class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                        ><?php echo e($setting['setting_value']); ?></textarea>
                    <?php else: ?>
                        <input 
                            type="text" 
                            name="settings[<?php echo $setting['setting_key']; ?>]"
                            value="<?php echo e($setting['setting_value']); ?>"
                            class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        >
                    <?php endif; ?>
                    <?php if ($setting['description']): ?>
                    <p class="text-xs text-gray-500 mt-1"><?php echo e($setting['description']); ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Appearance Settings -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold flex items-center">
                    <i class="fas fa-palette text-purple-500 mr-2"></i>
                    Apariencia
                </h2>
            </div>
            <div class="p-6 space-y-4">
                <?php foreach ($grouped['appearance'] as $setting): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo ucfirst(str_replace('_', ' ', $setting['setting_key'])); ?>
                    </label>
                    <div class="flex items-center space-x-4">
                        <input 
                            type="color" 
                            name="settings[<?php echo $setting['setting_key']; ?>]"
                            value="<?php echo e($setting['setting_value']); ?>"
                            class="h-12 w-20 border border-gray-300 rounded cursor-pointer"
                        >
                        <input 
                            type="text" 
                            value="<?php echo e($setting['setting_value']); ?>"
                            readonly
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
                        >
                    </div>
                    <?php if ($setting['description']): ?>
                    <p class="text-xs text-gray-500 mt-1"><?php echo e($setting['description']); ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Integration Settings -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold flex items-center">
                    <i class="fas fa-plug text-orange-500 mr-2"></i>
                    Integraciones
                </h2>
            </div>
            <div class="p-6 space-y-4">
                <?php foreach ($grouped['integrations'] as $setting): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo ucfirst(str_replace('_', ' ', $setting['setting_key'])); ?>
                    </label>
                    <?php if ($setting['setting_type'] === 'select'): ?>
                        <select 
                            name="settings[<?php echo $setting['setting_key']; ?>]"
                            class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="sandbox" <?php echo $setting['setting_value'] === 'sandbox' ? 'selected' : ''; ?>>Sandbox (Pruebas)</option>
                            <option value="live" <?php echo $setting['setting_value'] === 'live' ? 'selected' : ''; ?>>Live (Producción)</option>
                        </select>
                    <?php else: ?>
                        <input 
                            type="text" 
                            name="settings[<?php echo $setting['setting_key']; ?>]"
                            value="<?php echo e($setting['setting_value']); ?>"
                            class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        >
                    <?php endif; ?>
                    <?php if ($setting['description']): ?>
                    <p class="text-xs text-gray-500 mt-1"><?php echo e($setting['description']); ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- System Settings -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold flex items-center">
                    <i class="fas fa-server text-gray-500 mr-2"></i>
                    Configuraciones del Sistema
                </h2>
            </div>
            <div class="p-6 space-y-4">
                <?php foreach ($grouped['system'] as $setting): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo ucfirst(str_replace('_', ' ', $setting['setting_key'])); ?>
                    </label>
                    <?php if ($setting['setting_type'] === 'number'): ?>
                        <input 
                            type="number" 
                            name="settings[<?php echo $setting['setting_key']; ?>]"
                            value="<?php echo e($setting['setting_value']); ?>"
                            min="1"
                            class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        >
                    <?php elseif ($setting['setting_type'] === 'boolean'): ?>
                        <label class="inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="settings[<?php echo $setting['setting_key']; ?>]"
                                value="1"
                                <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>
                                class="sr-only peer"
                            >
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span class="ml-3 text-sm font-medium text-gray-700">Habilitado</span>
                        </label>
                    <?php else: ?>
                        <input 
                            type="text" 
                            name="settings[<?php echo $setting['setting_key']; ?>]"
                            value="<?php echo e($setting['setting_value']); ?>"
                            class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        >
                    <?php endif; ?>
                    <?php if ($setting['description']): ?>
                    <p class="text-xs text-gray-500 mt-1"><?php echo e($setting['description']); ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex justify-between items-center">
            <a href="<?php echo url('dashboard'); ?>" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-2"></i> Volver al Dashboard
            </a>
            <div class="flex space-x-4">
                <button 
                    type="submit" 
                    class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <i class="fas fa-save mr-2"></i>
                    Guardar Configuración
                </button>
            </div>
        </div>
    </div>
</form>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
