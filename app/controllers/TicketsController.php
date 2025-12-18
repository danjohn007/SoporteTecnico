<?php
/**
 * Tickets Controller
 * Handles ticket creation, viewing, and management
 */

require_once 'BaseController.php';

class TicketsController extends BaseController {
    private $ticketModel;
    private $categoryModel;
    
    public function __construct() {
        requireLogin();
        $this->ticketModel = $this->model('Ticket');
        $this->categoryModel = $this->model('Category');
    }
    
    /**
     * List all user tickets
     */
    public function index() {
        $userId = $_SESSION['user_id'];
        $estado = $this->getQuery('estado');
        
        // Validate estado against allowed values
        $allowedStates = ['abierto', 'en_proceso', 'en_espera_cliente', 'resuelto', 'cerrado'];
        
        $filters = [];
        if ($estado && in_array($estado, $allowedStates)) {
            $filters['estado'] = $estado;
        }
        
        $tickets = $this->ticketModel->getByUser($userId, $filters);
        
        $this->view('tickets/index', [
            'title' => 'Mis Tickets - ' . SITE_NAME,
            'tickets' => $tickets,
            'currentFilter' => $estado
        ]);
    }
    
    /**
     * Show create ticket form
     */
    public function create() {
        $categories = $this->categoryModel->getAll();
        
        $this->view('tickets/create', [
            'title' => 'Crear Ticket - ' . SITE_NAME,
            'categories' => $categories
        ]);
    }
    
    /**
     * Process ticket creation
     */
    public function store() {
        if (!$this->isPost()) {
            redirect('tickets/create');
        }
        
        $userId = $_SESSION['user_id'];
        $categoryId = $this->getPost('category_id');
        $asunto = sanitize($this->getPost('asunto'));
        $descripcion = sanitize($this->getPost('descripcion'));
        $prioridad = $this->getPost('prioridad', 'media');
        
        // Validate
        $errors = [];
        if (empty($categoryId)) $errors[] = 'Selecciona una categoría';
        if (empty($asunto)) $errors[] = 'El asunto es requerido';
        if (empty($descripcion)) $errors[] = 'La descripción es requerida';
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            redirect('tickets/create');
        }
        
        // Create ticket
        $result = $this->ticketModel->create([
            'user_id' => $userId,
            'category_id' => $categoryId,
            'asunto' => $asunto,
            'descripcion' => $descripcion,
            'prioridad' => $prioridad
        ]);
        
        if ($result['success']) {
            // Add initial message
            $this->ticketModel->addMessage($result['id'], $userId, $descripcion);
            
            // Handle file upload if present
            if (!empty($_FILES['attachment']['name'])) {
                $uploadResult = uploadFile($_FILES['attachment']);
                if ($uploadResult['success']) {
                    $this->ticketModel->addAttachment(
                        $result['id'],
                        null,
                        $uploadResult,
                        $userId
                    );
                }
            }
            
            // Create notification
            createNotification($userId, 'ticket_created', "Tu ticket {$result['ticket_id']} ha sido creado exitosamente", $result['id']);
            
            $_SESSION['success'] = "Ticket {$result['ticket_id']} creado exitosamente";
            redirect('tickets/view/' . $result['id']);
        } else {
            $_SESSION['errors'] = ['Error al crear el ticket. Intenta de nuevo.'];
            redirect('tickets/create');
        }
    }
    
    /**
     * View ticket details
     */
    public function view($ticketId) {
        $ticket = $this->ticketModel->findById($ticketId);
        
        if (!$ticket) {
            $_SESSION['errors'] = ['Ticket no encontrado'];
            redirect('tickets');
        }
        
        // Check permission
        $user = getCurrentUser();
        if ($ticket['user_id'] != $user['id'] && !isAgent()) {
            $_SESSION['errors'] = ['No tienes permiso para ver este ticket'];
            redirect('tickets');
        }
        
        // Get messages and attachments
        $messages = $this->ticketModel->getMessages($ticketId);
        $attachments = $this->ticketModel->getAttachments($ticketId);
        
        $this->view('tickets/view', [
            'title' => 'Ticket ' . $ticket['ticket_id'] . ' - ' . SITE_NAME,
            'ticket' => $ticket,
            'messages' => $messages,
            'attachments' => $attachments
        ]);
    }
    
    /**
     * Add reply to ticket
     */
    public function reply($ticketId) {
        if (!$this->isPost()) {
            redirect('tickets/view/' . $ticketId);
        }
        
        $ticket = $this->ticketModel->findById($ticketId);
        
        if (!$ticket) {
            $_SESSION['errors'] = ['Ticket no encontrado'];
            redirect('tickets');
        }
        
        $userId = $_SESSION['user_id'];
        $mensaje = sanitize($this->getPost('mensaje'));
        
        if (empty($mensaje)) {
            $_SESSION['errors'] = ['El mensaje no puede estar vacío'];
            redirect('tickets/view/' . $ticketId);
        }
        
        // Add message
        $result = $this->ticketModel->addMessage($ticketId, $userId, $mensaje);
        
        if ($result) {
            // Handle file upload if present
            if (!empty($_FILES['attachment']['name'])) {
                $uploadResult = uploadFile($_FILES['attachment']);
                if ($uploadResult['success']) {
                    $this->ticketModel->addAttachment(
                        $ticketId,
                        null,
                        $uploadResult,
                        $userId
                    );
                }
            }
            
            // Create notification for the other party
            if ($userId == $ticket['user_id'] && $ticket['agent_id']) {
                createNotification($ticket['agent_id'], 'ticket_reply', "El cliente respondió en el ticket {$ticket['ticket_id']}", $ticketId);
            } elseif ($userId != $ticket['user_id']) {
                createNotification($ticket['user_id'], 'ticket_reply', "El agente respondió en tu ticket {$ticket['ticket_id']}", $ticketId);
            }
            
            $_SESSION['success'] = 'Respuesta enviada correctamente';
        } else {
            $_SESSION['errors'] = ['Error al enviar la respuesta'];
        }
        
        redirect('tickets/view/' . $ticketId);
    }
    
    /**
     * Change ticket status (agents only)
     */
    public function changeStatus($ticketId) {
        requireRole(ROLE_AGENT);
        
        if (!$this->isPost()) {
            redirect('tickets/view/' . $ticketId);
        }
        
        $status = $this->getPost('status');
        
        $result = $this->ticketModel->changeStatus($ticketId, $status);
        
        if ($result) {
            $ticket = $this->ticketModel->findById($ticketId);
            createNotification($ticket['user_id'], 'ticket_status_changed', "El estado de tu ticket {$ticket['ticket_id']} cambió a: " . getStatusLabel($status), $ticketId);
            
            $_SESSION['success'] = 'Estado actualizado correctamente';
        } else {
            $_SESSION['errors'] = ['Error al actualizar el estado'];
        }
        
        redirect('tickets/view/' . $ticketId);
    }
    
    /**
     * Assign ticket to agent (agents/admin only)
     */
    public function assign($ticketId) {
        requireRole(ROLE_AGENT);
        
        if (!$this->isPost()) {
            redirect('tickets/view/' . $ticketId);
        }
        
        $agentId = $this->getPost('agent_id', $_SESSION['user_id']);
        
        $result = $this->ticketModel->assign($ticketId, $agentId);
        
        if ($result) {
            $ticket = $this->ticketModel->findById($ticketId);
            createNotification($agentId, 'ticket_assigned', "Se te ha asignado el ticket {$ticket['ticket_id']}", $ticketId);
            createNotification($ticket['user_id'], 'ticket_assigned', "Tu ticket {$ticket['ticket_id']} ha sido asignado a un agente", $ticketId);
            
            $_SESSION['success'] = 'Ticket asignado correctamente';
        } else {
            $_SESSION['errors'] = ['Error al asignar el ticket'];
        }
        
        redirect('tickets/view/' . $ticketId);
    }
}
