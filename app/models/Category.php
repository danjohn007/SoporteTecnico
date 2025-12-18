<?php
/**
 * Category Model
 * Handles category-related database operations
 */

class Category {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all active categories
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY nombre ASC");
        return $stmt->fetchAll();
    }
    
    /**
     * Get category by ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Create category
     */
    public function create($nombre, $descripcion) {
        try {
            $stmt = $this->db->prepare("INSERT INTO categories (nombre, descripcion) VALUES (?, ?)");
            $stmt->execute([$nombre, $descripcion]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Category create error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update category
     */
    public function update($id, $nombre, $descripcion) {
        try {
            $stmt = $this->db->prepare("UPDATE categories SET nombre = ?, descripcion = ? WHERE id = ?");
            return $stmt->execute([$nombre, $descripcion, $id]);
        } catch (PDOException $e) {
            error_log("Category update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete (deactivate) category
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("UPDATE categories SET is_active = 0 WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Category delete error: " . $e->getMessage());
            return false;
        }
    }
}
