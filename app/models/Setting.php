<?php
/**
 * Setting Model
 * Handles system settings
 */

class Setting {
    private $db;
    private static $cache = [];
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get setting value by key
     */
    public function get($key, $default = null) {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }
        
        $stmt = $this->db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        
        $value = $result ? $result['setting_value'] : $default;
        self::$cache[$key] = $value;
        
        return $value;
    }
    
    /**
     * Set setting value
     */
    public function set($key, $value, $type = 'text', $description = '') {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO system_settings (setting_key, setting_value, setting_type, description) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE setting_value = ?, setting_type = ?, description = ?
            ");
            $result = $stmt->execute([$key, $value, $type, $description, $value, $type, $description]);
            
            if ($result) {
                self::$cache[$key] = $value;
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Setting set error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all settings
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM system_settings ORDER BY setting_key ASC");
        return $stmt->fetchAll();
    }
    
    /**
     * Get settings by type
     */
    public function getByType($type) {
        $stmt = $this->db->prepare("SELECT * FROM system_settings WHERE setting_type = ? ORDER BY setting_key ASC");
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }
    
    /**
     * Delete setting
     */
    public function delete($key) {
        try {
            $stmt = $this->db->prepare("DELETE FROM system_settings WHERE setting_key = ?");
            $result = $stmt->execute([$key]);
            
            if ($result) {
                unset(self::$cache[$key]);
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Setting delete error: " . $e->getMessage());
            return false;
        }
    }
}
