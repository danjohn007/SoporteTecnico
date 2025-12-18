<?php
/**
 * Chatbot Model
 * Handles chatbot conversations and FAQ matching
 */

class Chatbot {
    private $db;
    private $faq;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->faq = new FAQ();
    }
    
    /**
     * Process user message and generate response
     */
    public function processMessage($userId, $message, $sessionId = null) {
        if (!$sessionId) {
            $sessionId = $this->generateSessionId($userId);
        }
        
        // Search for matching FAQs
        $faqs = $this->faq->search($message);
        
        $response = null;
        $faqId = null;
        
        if (!empty($faqs)) {
            // Return the best matching FAQ
            $bestMatch = $faqs[0];
            $faqId = $bestMatch['id'];
            $response = $this->formatFaqResponse($bestMatch);
            
            // Increment FAQ views
            $this->faq->incrementViews($faqId);
        } else {
            // No match found - offer to create ticket
            $response = $this->getNoMatchResponse();
        }
        
        // Log conversation
        $this->logConversation($userId, $sessionId, $message, $response, $faqId);
        
        return [
            'response' => $response,
            'faqs' => $faqs,
            'session_id' => $sessionId,
            'has_match' => !empty($faqs)
        ];
    }
    
    /**
     * Format FAQ response
     */
    private function formatFaqResponse($faq) {
        return [
            'type' => 'faq',
            'id' => $faq['id'],
            'pregunta' => $faq['pregunta'],
            'respuesta' => $faq['respuesta'],
            'categoria' => $faq['categoria_nombre'] ?? 'General'
        ];
    }
    
    /**
     * Get response when no FAQ matches
     */
    private function getNoMatchResponse() {
        return [
            'type' => 'no_match',
            'message' => 'Lo siento, no encontré información específica sobre tu consulta. ¿Te gustaría crear un ticket de soporte para que un agente te ayude?',
            'suggestions' => [
                'Crear un ticket de soporte',
                'Ver todas las preguntas frecuentes',
                'Intentar con otra pregunta'
            ]
        ];
    }
    
    /**
     * Generate session ID
     */
    private function generateSessionId($userId) {
        return 'chat_' . $userId . '_' . time();
    }
    
    /**
     * Log conversation
     */
    private function logConversation($userId, $sessionId, $message, $response, $faqId = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO chatbot_conversations (user_id, session_id, message, response, faq_id) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $responseJson = is_array($response) ? json_encode($response) : $response;
            $stmt->execute([$userId, $sessionId, $message, $responseJson, $faqId]);
            return true;
        } catch (PDOException $e) {
            error_log("Chatbot log error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get conversation history
     */
    public function getHistory($userId, $sessionId = null, $limit = 20) {
        if ($sessionId) {
            $stmt = $this->db->prepare("
                SELECT * FROM chatbot_conversations 
                WHERE user_id = ? AND session_id = ?
                ORDER BY created_at ASC
                LIMIT ?
            ");
            $stmt->execute([$userId, $sessionId, $limit]);
        } else {
            $stmt = $this->db->prepare("
                SELECT * FROM chatbot_conversations 
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$userId, $limit]);
        }
        return $stmt->fetchAll();
    }
    
    /**
     * Create ticket from conversation
     */
    public function createTicketFromConversation($userId, $sessionId, $categoryId, $asunto, $descripcion, $prioridad = 'media') {
        // Get conversation history
        $history = $this->getHistory($userId, $sessionId);
        
        // Format history into description
        $fullDescription = $descripcion . "\n\n--- Historial del Chat ---\n\n";
        foreach ($history as $conv) {
            $fullDescription .= "Usuario: " . $conv['message'] . "\n";
            $response = json_decode($conv['response'], true);
            if (is_array($response) && isset($response['respuesta'])) {
                $fullDescription .= "Bot: " . $response['respuesta'] . "\n\n";
            }
        }
        
        // Create ticket
        $ticketModel = new Ticket();
        $result = $ticketModel->create([
            'user_id' => $userId,
            'category_id' => $categoryId,
            'asunto' => $asunto,
            'descripcion' => $fullDescription,
            'prioridad' => $prioridad
        ]);
        
        if ($result['success']) {
            // Link conversation to ticket
            $this->linkToTicket($sessionId, $result['id']);
        }
        
        return $result;
    }
    
    /**
     * Link conversation to ticket
     */
    private function linkToTicket($sessionId, $ticketId) {
        try {
            $stmt = $this->db->prepare("
                UPDATE chatbot_conversations 
                SET created_ticket_id = ? 
                WHERE session_id = ?
            ");
            return $stmt->execute([$ticketId, $sessionId]);
        } catch (PDOException $e) {
            error_log("Link to ticket error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get suggested questions based on popular FAQs
     */
    public function getSuggestedQuestions($limit = 5) {
        return $this->faq->getPopular($limit);
    }
}
