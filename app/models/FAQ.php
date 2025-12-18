<?php
/**
 * FAQ Model
 * Handles FAQ-related database operations
 */

class FAQ {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all active FAQs
     */
    public function getAll($categoryId = null) {
        if ($categoryId) {
            $stmt = $this->db->prepare("
                SELECT f.*, c.nombre as categoria_nombre
                FROM faqs f
                LEFT JOIN categories c ON f.category_id = c.id
                WHERE f.is_active = 1 AND f.category_id = ?
                ORDER BY f.helpful_count DESC, f.views DESC
            ");
            $stmt->execute([$categoryId]);
        } else {
            $stmt = $this->db->query("
                SELECT f.*, c.nombre as categoria_nombre
                FROM faqs f
                LEFT JOIN categories c ON f.category_id = c.id
                WHERE f.is_active = 1
                ORDER BY f.helpful_count DESC, f.views DESC
            ");
        }
        return $stmt->fetchAll();
    }
    
    /**
     * Find FAQ by ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT f.*, c.nombre as categoria_nombre
            FROM faqs f
            LEFT JOIN categories c ON f.category_id = c.id
            WHERE f.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Search FAQs
     */
    public function search($query) {
        $searchTerm = '%' . $query . '%';
        $stmt = $this->db->prepare("
            SELECT f.*, c.nombre as categoria_nombre
            FROM faqs f
            LEFT JOIN categories c ON f.category_id = c.id
            WHERE f.is_active = 1 
            AND (f.pregunta LIKE ? OR f.respuesta LIKE ? OR f.keywords LIKE ?)
            ORDER BY f.helpful_count DESC
            LIMIT 10
        ");
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }
    
    /**
     * Create FAQ
     */
    public function create($categoryId, $pregunta, $respuesta, $keywords = '') {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO faqs (category_id, pregunta, respuesta, keywords) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$categoryId, $pregunta, $respuesta, $keywords]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("FAQ create error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update FAQ
     */
    public function update($id, $categoryId, $pregunta, $respuesta, $keywords = '') {
        try {
            $stmt = $this->db->prepare("
                UPDATE faqs 
                SET category_id = ?, pregunta = ?, respuesta = ?, keywords = ?
                WHERE id = ?
            ");
            return $stmt->execute([$categoryId, $pregunta, $respuesta, $keywords, $id]);
        } catch (PDOException $e) {
            error_log("FAQ update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete (deactivate) FAQ
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("UPDATE faqs SET is_active = 0 WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("FAQ delete error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Increment views
     */
    public function incrementViews($id) {
        $stmt = $this->db->prepare("UPDATE faqs SET views = views + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Mark as helpful
     */
    public function markHelpful($id, $isHelpful = true) {
        $field = $isHelpful ? 'helpful_count' : 'not_helpful_count';
        $stmt = $this->db->prepare("UPDATE faqs SET $field = $field + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get popular FAQs
     */
    public function getPopular($limit = 5) {
        $stmt = $this->db->prepare("
            SELECT f.*, c.nombre as categoria_nombre
            FROM faqs f
            LEFT JOIN categories c ON f.category_id = c.id
            WHERE f.is_active = 1
            ORDER BY f.views DESC, f.helpful_count DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
