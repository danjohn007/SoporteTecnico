<?php ob_start(); ?>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6">
            <h1 class="text-2xl font-bold flex items-center">
                <i class="fas fa-robot mr-3 text-3xl"></i>
                Asistente Virtual de Soporte
            </h1>
            <p class="mt-2 text-green-100">Pregúntame lo que necesites, estoy aquí para ayudarte</p>
        </div>
        
        <!-- Chat Container -->
        <div id="chat-container" class="h-96 overflow-y-auto p-6 bg-gray-50">
            <!-- Welcome Message -->
            <div class="flex items-start mb-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white">
                        <i class="fas fa-robot"></i>
                    </div>
                </div>
                <div class="ml-3 bg-white rounded-lg p-4 shadow">
                    <p class="text-gray-800">
                        ¡Hola! 👋 Soy tu asistente virtual. Puedo ayudarte a encontrar respuestas rápidas en nuestra base de conocimiento.
                    </p>
                    <p class="text-gray-600 text-sm mt-2">
                        Escribe tu pregunta o selecciona una de las sugerencias:
                    </p>
                </div>
            </div>
            
            <!-- Suggested Questions -->
            <?php if (!empty($suggestedQuestions)): ?>
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2 px-2">Preguntas frecuentes:</p>
                <div class="space-y-2">
                    <?php foreach ($suggestedQuestions as $faq): ?>
                    <button 
                        onclick="askQuestion('<?php echo addslashes($faq['pregunta']); ?>')"
                        class="w-full text-left px-4 py-2 bg-white hover:bg-blue-50 border border-gray-200 rounded-lg text-sm transition"
                    >
                        <i class="fas fa-chevron-right text-blue-500 mr-2"></i>
                        <?php echo e($faq['pregunta']); ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Input Area -->
        <div class="border-t border-gray-200 p-4 bg-white">
            <form id="chat-form" class="flex space-x-2">
                <input 
                    type="text" 
                    id="message-input"
                    placeholder="Escribe tu pregunta aquí..."
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    autocomplete="off"
                >
                <button 
                    type="submit"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                >
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
            <p class="text-xs text-gray-500 mt-2">
                <i class="fas fa-lock mr-1"></i>
                Tus conversaciones son privadas y seguras
            </p>
        </div>
    </div>
    
    <!-- Info Cards -->
    <div class="grid md:grid-cols-2 gap-4 mt-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                ¿No encontraste tu respuesta?
            </h3>
            <p class="text-sm text-gray-600 mb-3">
                Si el chatbot no puede ayudarte, crea un ticket y un agente te asistirá personalmente.
            </p>
            <a href="<?php echo url('tickets/create'); ?>" class="inline-block bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                Crear Ticket
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                <i class="fas fa-book text-purple-500 mr-2"></i>
                Explora todas las FAQs
            </h3>
            <p class="text-sm text-gray-600 mb-3">
                Consulta nuestra base de conocimiento completa con todas las preguntas frecuentes.
            </p>
            <a href="<?php echo url('faq'); ?>" class="inline-block bg-purple-600 text-white px-4 py-2 rounded text-sm hover:bg-purple-700">
                Ver FAQs
            </a>
        </div>
    </div>
</div>

<script>
let sessionId = 'chat_' + Date.now();

document.getElementById('chat-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const input = document.getElementById('message-input');
    const message = input.value.trim();
    
    if (!message) return;
    
    // Add user message to chat
    addMessageToChat(message, 'user');
    input.value = '';
    
    // Show typing indicator
    showTypingIndicator();
    
    try {
        // Send message to chatbot
        const response = await fetch('<?php echo url('chatbot/message'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `message=${encodeURIComponent(message)}&session_id=${sessionId}`
        });
        
        const data = await response.json();
        
        // Remove typing indicator
        removeTypingIndicator();
        
        // Add bot response
        if (data.has_match && data.faqs && data.faqs.length > 0) {
            const faq = data.faqs[0];
            addFaqToChat(faq);
        } else if (data.response) {
            addBotMessage(data.response.message);
        }
        
    } catch (error) {
        removeTypingIndicator();
        addBotMessage('Lo siento, hubo un error. Por favor intenta de nuevo.');
    }
    
    // Scroll to bottom
    scrollToBottom();
});

function askQuestion(question) {
    document.getElementById('message-input').value = question;
    document.getElementById('chat-form').dispatchEvent(new Event('submit'));
}

function addMessageToChat(message, type) {
    const container = document.getElementById('chat-container');
    const isUser = type === 'user';
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `flex items-start mb-4 ${isUser ? 'justify-end' : ''}`;
    
    messageDiv.innerHTML = `
        ${!isUser ? `
        <div class="flex-shrink-0">
            <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white">
                <i class="fas fa-robot"></i>
            </div>
        </div>
        ` : ''}
        <div class="${isUser ? 'mr-3' : 'ml-3'} ${isUser ? 'bg-blue-600 text-white' : 'bg-white'} rounded-lg p-4 shadow max-w-md">
            <p>${message}</p>
        </div>
        ${isUser ? `
        <div class="flex-shrink-0">
            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white">
                <i class="fas fa-user"></i>
            </div>
        </div>
        ` : ''}
    `;
    
    container.appendChild(messageDiv);
}

function addFaqToChat(faq) {
    const container = document.getElementById('chat-container');
    
    const messageDiv = document.createElement('div');
    messageDiv.className = 'flex items-start mb-4';
    
    messageDiv.innerHTML = `
        <div class="flex-shrink-0">
            <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white">
                <i class="fas fa-robot"></i>
            </div>
        </div>
        <div class="ml-3 bg-white rounded-lg p-4 shadow max-w-md">
            <p class="font-semibold text-gray-900 mb-2">${faq.pregunta}</p>
            <p class="text-gray-700">${faq.respuesta}</p>
            ${faq.categoria_nombre ? `<p class="text-xs text-gray-500 mt-2"><i class="fas fa-folder mr-1"></i>${faq.categoria_nombre}</p>` : ''}
            <div class="mt-3 pt-3 border-t border-gray-200 flex items-center space-x-2 text-xs">
                <span class="text-gray-600">¿Te fue útil?</span>
                <button onclick="markHelpful(${faq.id}, true)" class="text-green-600 hover:text-green-700">
                    <i class="fas fa-thumbs-up"></i> Sí
                </button>
                <button onclick="markHelpful(${faq.id}, false)" class="text-red-600 hover:text-red-700">
                    <i class="fas fa-thumbs-down"></i> No
                </button>
            </div>
        </div>
    `;
    
    container.appendChild(messageDiv);
}

function addBotMessage(message) {
    addMessageToChat(message, 'bot');
}

function showTypingIndicator() {
    const container = document.getElementById('chat-container');
    const indicator = document.createElement('div');
    indicator.id = 'typing-indicator';
    indicator.className = 'flex items-start mb-4';
    indicator.innerHTML = `
        <div class="flex-shrink-0">
            <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white">
                <i class="fas fa-robot"></i>
            </div>
        </div>
        <div class="ml-3 bg-white rounded-lg p-4 shadow">
            <div class="flex space-x-2">
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        </div>
    `;
    container.appendChild(indicator);
}

function removeTypingIndicator() {
    const indicator = document.getElementById('typing-indicator');
    if (indicator) {
        indicator.remove();
    }
}

function scrollToBottom() {
    const container = document.getElementById('chat-container');
    container.scrollTop = container.scrollHeight;
}

async function markHelpful(faqId, helpful) {
    try {
        await fetch(`<?php echo url('faq/helpful/'); ?>${faqId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `helpful=${helpful ? 'yes' : 'no'}`
        });
        addBotMessage('¡Gracias por tu feedback! 👍');
        scrollToBottom();
    } catch (error) {
        console.error('Error submitting feedback:', error);
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
