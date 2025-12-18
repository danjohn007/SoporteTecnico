<?php
/**
 * User Model
 * Handles user-related database operations
 */

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create a new user
     */
    public function create($nombreCompleto, $whatsapp, $role = 'user') {
        try {
            $stmt = $this->db->prepare("INSERT INTO users (nombre_completo, whatsapp, role) VALUES (?, ?, ?)");
            $stmt->execute([$nombreCompleto, $whatsapp, $role]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("User create error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find user by WhatsApp
     */
    public function findByWhatsApp($whatsapp) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE whatsapp = ? AND is_active = 1");
        $stmt->execute([$whatsapp]);
        return $stmt->fetch();
    }
    
    /**
     * Find user by ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? AND is_active = 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get all users
     */
    public function getAll($role = null) {
        if ($role) {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE role = ? ORDER BY created_at DESC");
            $stmt->execute([$role]);
        } else {
            $stmt = $this->db->query("SELECT * FROM users ORDER BY created_at DESC");
        }
        return $stmt->fetchAll();
    }
    
    /**
     * Update user
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        
        $values[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($values);
        } catch (PDOException $e) {
            error_log("User update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Deactivate user
     */
    public function deactivate($id) {
        return $this->update($id, ['is_active' => 0]);
    }
    
    /**
     * Get user statistics
     */
    public function getStats($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_tickets,
                SUM(CASE WHEN estado = 'abierto' THEN 1 ELSE 0 END) as tickets_abiertos,
                SUM(CASE WHEN estado = 'en_proceso' THEN 1 ELSE 0 END) as tickets_en_proceso,
                SUM(CASE WHEN estado = 'resuelto' THEN 1 ELSE 0 END) as tickets_resueltos
            FROM tickets 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
}
