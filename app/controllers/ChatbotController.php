<?php
/**
 * Chatbot Controller
 * Handles chatbot interactions
 */

require_once 'BaseController.php';

class ChatbotController extends BaseController {
    private $chatbotModel;
    private $categoryModel;
    
    public function __construct() {
        $this->chatbotModel = $this->model('Chatbot');
        $this->categoryModel = $this->model('Category');
    }
    
    /**
     * Chatbot interface
     */
    public function index() {
        $suggestedQuestions = $this->chatbotModel->getSuggestedQuestions(5);
        
        $this->view('chatbot/index', [
            'title' => 'Chatbot de Soporte - ' . SITE_NAME,
            'suggestedQuestions' => $suggestedQuestions
        ]);
    }
    
    /**
     * Process chatbot message (AJAX)
     */
    public function message() {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->json(['error' => 'Invalid request'], 400);
        }
        
        $message = sanitize($this->getPost('message'));
        $sessionId = $this->getPost('session_id');
        
        if (empty($message)) {
            $this->json(['error' => 'Message is required'], 400);
        }
        
        // Get user ID if logged in
        $userId = $_SESSION['user_id'] ?? null;
        
        // If not logged in, use a guest ID based on session
        if (!$userId) {
            if (!isset($_SESSION['guest_id'])) {
                $_SESSION['guest_id'] = 'guest_' . uniqid();
            }
            $userId = $_SESSION['guest_id'];
        }
        
        // Process message
        $response = $this->chatbotModel->processMessage($userId, $message, $sessionId);
        
        $this->json($response);
    }
    
    /**
     * Create ticket from chat
     */
    public function createTicket() {
        requireLogin();
        
        if (!$this->isPost()) {
            redirect('chatbot');
        }
        
        $sessionId = $this->getPost('session_id');
        $categoryId = $this->getPost('category_id');
        $asunto = sanitize($this->getPost('asunto'));
        $descripcion = sanitize($this->getPost('descripcion'));
        $prioridad = $this->getPost('prioridad', 'media');
        
        $userId = $_SESSION['user_id'];
        
        // Create ticket from conversation
        $result = $this->chatbotModel->createTicketFromConversation(
            $userId,
            $sessionId,
            $categoryId,
            $asunto,
            $descripcion,
            $prioridad
        );
        
        if ($result['success']) {
            if ($this->isAjax()) {
                $this->json([
                    'success' => true,
                    'ticket_id' => $result['ticket_id'],
                    'message' => 'Ticket creado exitosamente'
                ]);
            } else {
                $_SESSION['success'] = "Ticket {$result['ticket_id']} creado desde el chat";
                redirect('tickets/view/' . $result['id']);
            }
        } else {
            if ($this->isAjax()) {
                $this->json(['error' => 'Error al crear el ticket'], 500);
            } else {
                $_SESSION['errors'] = ['Error al crear el ticket'];
                redirect('chatbot');
            }
        }
    }
    
    /**
     * Get chat history
     */
    public function history() {
        requireLogin();
        
        if (!$this->isAjax()) {
            redirect('chatbot');
        }
        
        $userId = $_SESSION['user_id'];
        $sessionId = $this->getQuery('session_id');
        
        $history = $this->chatbotModel->getHistory($userId, $sessionId);
        
        $this->json(['history' => $history]);
    }
}
