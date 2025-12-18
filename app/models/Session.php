<?php
/**
 * Session Model
 * Handles user session management
 */

class Session {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create a new session
     */
    public function create($userId) {
        $token = generateToken();
        $expiresAt = date('Y-m-d H:i:s', time() + SESSION_LIFETIME);
        
        try {
            $stmt = $this->db->prepare("INSERT INTO sessions (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $token, $expiresAt]);
            return $token;
        } catch (PDOException $e) {
            error_log("Session create error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validate session token
     */
    public function validate($token) {
        $stmt = $this->db->prepare("
            SELECT s.*, u.* 
            FROM sessions s
            JOIN users u ON s.user_id = u.id
            WHERE s.token = ? AND s.expires_at > NOW() AND u.is_active = 1
        ");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }
    
    /**
     * Delete session
     */
    public function delete($token) {
        $stmt = $this->db->prepare("DELETE FROM sessions WHERE token = ?");
        return $stmt->execute([$token]);
    }
    
    /**
     * Delete all sessions for a user
     */
    public function deleteAllForUser($userId) {
        $stmt = $this->db->prepare("DELETE FROM sessions WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
    
    /**
     * Clean expired sessions
     */
    public function cleanExpired() {
        $stmt = $this->db->prepare("DELETE FROM sessions WHERE expires_at < NOW()");
        return $stmt->execute();
    }
}
