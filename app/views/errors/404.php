<?php ob_start(); ?>

<div class="min-h-screen flex items-center justify-center">
    <div class="text-center">
        <div class="text-9xl font-bold text-blue-500 mb-4">404</div>
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Página No Encontrada</h1>
        <p class="text-xl text-gray-600 mb-8">
            Lo sentimos, la página que buscas no existe.
        </p>
        <a href="<?php echo url(); ?>" class="inline-block bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition">
            <i class="fas fa-home mr-2"></i> Volver al Inicio
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
