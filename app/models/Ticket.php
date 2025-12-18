<?php
/**
 * Ticket Model
 * Handles ticket-related database operations
 */

class Ticket {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create a new ticket
     */
    public function create($data) {
        try {
            $ticketId = generateTicketId();
            
            // Calculate SLA hours based on priority
            $slaHours = $this->getSlaHours($data['prioridad']);
            
            $stmt = $this->db->prepare("
                INSERT INTO tickets (ticket_id, user_id, category_id, asunto, descripcion, prioridad, sla_hours) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $ticketId,
                $data['user_id'],
                $data['category_id'],
                $data['asunto'],
                $data['descripcion'],
                $data['prioridad'],
                $slaHours
            ]);
            
            $id = $this->db->lastInsertId();
            
            // Log the creation
            logAudit('TICKET_CREATED', 'ticket', $id, "Ticket $ticketId created");
            
            return ['success' => true, 'id' => $id, 'ticket_id' => $ticketId];
        } catch (PDOException $e) {
            error_log("Ticket create error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Get SLA hours by priority
     */
    private function getSlaHours($priority) {
        $sla = [
            'critica' => SLA_CRITICAL,
            'alta' => SLA_HIGH,
            'media' => SLA_MEDIUM,
            'baja' => SLA_LOW
        ];
        return $sla[$priority] ?? SLA_MEDIUM;
    }
    
    /**
     * Find ticket by ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT t.*, 
                   u.nombre_completo as usuario_nombre, u.whatsapp as usuario_whatsapp,
                   a.nombre_completo as agente_nombre,
                   c.nombre as categoria_nombre
            FROM tickets t
            JOIN users u ON t.user_id = u.id
            LEFT JOIN users a ON t.agent_id = a.id
            JOIN categories c ON t.category_id = c.id
            WHERE t.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Find ticket by ticket_id
     */
    public function findByTicketId($ticketId) {
        $stmt = $this->db->prepare("
            SELECT t.*, 
                   u.nombre_completo as usuario_nombre, u.whatsapp as usuario_whatsapp,
                   a.nombre_completo as agente_nombre,
                   c.nombre as categoria_nombre
            FROM tickets t
            JOIN users u ON t.user_id = u.id
            LEFT JOIN users a ON t.agent_id = a.id
            JOIN categories c ON t.category_id = c.id
            WHERE t.ticket_id = ?
        ");
        $stmt->execute([$ticketId]);
        return $stmt->fetch();
    }
    
    /**
     * Get tickets by user
     */
    public function getByUser($userId, $filters = []) {
        $sql = "
            SELECT t.*, 
                   c.nombre as categoria_nombre,
                   a.nombre_completo as agente_nombre
            FROM tickets t
            JOIN categories c ON t.category_id = c.id
            LEFT JOIN users a ON t.agent_id = a.id
            WHERE t.user_id = ?
        ";
        
        $params = [$userId];
        
        if (!empty($filters['estado'])) {
            $sql .= " AND t.estado = ?";
            $params[] = $filters['estado'];
        }
        
        if (!empty($filters['prioridad'])) {
            $sql .= " AND t.prioridad = ?";
            $params[] = $filters['prioridad'];
        }
        
        $sql .= " ORDER BY t.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get tickets by agent
     */
    public function getByAgent($agentId, $filters = []) {
        $sql = "
            SELECT t.*, 
                   u.nombre_completo as usuario_nombre,
                   c.nombre as categoria_nombre
            FROM tickets t
            JOIN users u ON t.user_id = u.id
            JOIN categories c ON t.category_id = c.id
            WHERE t.agent_id = ?
        ";
        
        $params = [$agentId];
        
        if (!empty($filters['estado'])) {
            $sql .= " AND t.estado = ?";
            $params[] = $filters['estado'];
        }
        
        $sql .= " ORDER BY t.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get all tickets (for admin/agent)
     */
    public function getAll($filters = [], $limit = 50, $offset = 0) {
        $sql = "
            SELECT t.*, 
                   u.nombre_completo as usuario_nombre,
                   a.nombre_completo as agente_nombre,
                   c.nombre as categoria_nombre
            FROM tickets t
            JOIN users u ON t.user_id = u.id
            LEFT JOIN users a ON t.agent_id = a.id
            JOIN categories c ON t.category_id = c.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($filters['estado'])) {
            $sql .= " AND t.estado = ?";
            $params[] = $filters['estado'];
        }
        
        if (!empty($filters['prioridad'])) {
            $sql .= " AND t.prioridad = ?";
            $params[] = $filters['prioridad'];
        }
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND t.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['agent_id'])) {
            $sql .= " AND t.agent_id = ?";
            $params[] = $filters['agent_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (t.ticket_id LIKE ? OR t.asunto LIKE ? OR t.descripcion LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " ORDER BY t.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Update ticket
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        
        $values[] = $id;
        $sql = "UPDATE tickets SET " . implode(', ', $fields) . " WHERE id = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($values);
            
            if ($result && isset($data['estado'])) {
                logAudit('TICKET_STATUS_CHANGED', 'ticket', $id, "Status changed to: " . $data['estado']);
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Ticket update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Assign ticket to agent
     */
    public function assign($ticketId, $agentId) {
        $result = $this->update($ticketId, [
            'agent_id' => $agentId,
            'estado' => 'en_proceso'
        ]);
        
        if ($result) {
            logAudit('TICKET_ASSIGNED', 'ticket', $ticketId, "Assigned to agent: $agentId");
        }
        
        return $result;
    }
    
    /**
     * Change ticket status
     */
    public function changeStatus($ticketId, $status) {
        $data = ['estado' => $status];
        
        if ($status === 'cerrado') {
            $data['closed_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->update($ticketId, $data);
    }
    
    /**
     * Get ticket messages
     */
    public function getMessages($ticketId) {
        $stmt = $this->db->prepare("
            SELECT m.*, u.nombre_completo, u.role
            FROM ticket_messages m
            JOIN users u ON m.user_id = u.id
            WHERE m.ticket_id = ?
            ORDER BY m.created_at ASC
        ");
        $stmt->execute([$ticketId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Add message to ticket
     */
    public function addMessage($ticketId, $userId, $mensaje, $isInternal = false) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO ticket_messages (ticket_id, user_id, mensaje, is_internal) 
                VALUES (?, ?, ?, ?)
            ");
            $result = $stmt->execute([$ticketId, $userId, $mensaje, $isInternal ? 1 : 0]);
            
            if ($result) {
                // Update ticket last_activity
                $this->db->prepare("UPDATE tickets SET last_activity = NOW() WHERE id = ?")->execute([$ticketId]);
                
                logAudit('TICKET_MESSAGE_ADDED', 'ticket', $ticketId, "Message added");
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Add message error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get ticket attachments
     */
    public function getAttachments($ticketId) {
        $stmt = $this->db->prepare("
            SELECT a.*, u.nombre_completo as uploaded_by_name
            FROM ticket_attachments a
            JOIN users u ON a.uploaded_by = u.id
            WHERE a.ticket_id = ?
            ORDER BY a.created_at DESC
        ");
        $stmt->execute([$ticketId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Add attachment to ticket
     */
    public function addAttachment($ticketId, $messageId, $fileData, $uploadedBy) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO ticket_attachments 
                (ticket_id, message_id, filename, original_filename, file_path, file_type, file_size, uploaded_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            return $stmt->execute([
                $ticketId,
                $messageId,
                $fileData['filename'],
                $fileData['original_filename'],
                $fileData['path'],
                $fileData['type'],
                $fileData['size'],
                $uploadedBy
            ]);
        } catch (PDOException $e) {
            error_log("Add attachment error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get ticket statistics
     */
    public function getStatistics($filters = []) {
        $sql = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN estado = 'abierto' THEN 1 ELSE 0 END) as abiertos,
                SUM(CASE WHEN estado = 'en_proceso' THEN 1 ELSE 0 END) as en_proceso,
                SUM(CASE WHEN estado = 'en_espera_cliente' THEN 1 ELSE 0 END) as en_espera,
                SUM(CASE WHEN estado = 'resuelto' THEN 1 ELSE 0 END) as resueltos,
                SUM(CASE WHEN estado = 'cerrado' THEN 1 ELSE 0 END) as cerrados,
                SUM(CASE WHEN prioridad = 'critica' THEN 1 ELSE 0 END) as criticos,
                SUM(CASE WHEN prioridad = 'alta' THEN 1 ELSE 0 END) as altos
            FROM tickets
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($filters['agent_id'])) {
            $sql .= " AND agent_id = ?";
            $params[] = $filters['agent_id'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND created_at >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND created_at <= ?";
            $params[] = $filters['date_to'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    /**
     * Auto-close inactive tickets
     */
    public function autoCloseInactive() {
        $days = AUTO_CLOSE_DAYS;
        $stmt = $this->db->prepare("
            UPDATE tickets 
            SET estado = 'cerrado', closed_at = NOW()
            WHERE estado IN ('resuelto', 'en_espera_cliente') 
            AND last_activity < DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
        return $stmt->execute([$days]);
    }
}
