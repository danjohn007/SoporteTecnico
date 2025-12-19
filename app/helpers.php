<?php
/**
 * Helper Functions
 * Global utility functions for the application
 */

/**
 * Redirect to a URL
 */
function redirect($path = '') {
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['token']);
}

/**
 * Get current user
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return $_SESSION['user_data'] ?? null;
}

/**
 * Check if user has a specific role
 */
function hasRole($role) {
    $user = getCurrentUser();
    return $user && $user['role'] === $role;
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return hasRole(ROLE_ADMIN);
}

/**
 * Check if user is agent
 */
function isAgent() {
    return hasRole(ROLE_AGENT) || isAdmin();
}

/**
 * Require login - redirect if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect('auth/login');
    }
}

/**
 * Require specific role
 */
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        redirect('dashboard');
    }
}

/**
 * Generate CSRF token
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize input
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate WhatsApp number format
 */
function validateWhatsApp($number) {
    // Remove all non-numeric characters
    $clean = preg_replace('/[^0-9+]/', '', $number);
    
    // Check if it starts with + and has 10-15 digits
    if (preg_match('/^\+\d{10,15}$/', $clean)) {
        return $clean;
    }
    
    // If it doesn't start with +, add +52 for Mexico
    if (preg_match('/^\d{10}$/', $clean)) {
        return '+52' . $clean;
    }
    
    return false;
}

/**
 * Generate unique ticket ID
 */
function generateTicketId() {
    return 'TKT-' . strtoupper(substr(uniqid(), -6));
}

/**
 * Generate session token
 */
function generateToken() {
    return bin2hex(random_bytes(32));
}

/**
 * Format date in Spanish
 */
function formatDate($date, $format = 'd/m/Y H:i') {
    $timestamp = is_string($date) ? strtotime($date) : $date;
    if ($timestamp === false) {
        return 'Fecha inválida';
    }
    return date($format, $timestamp);
}

/**
 * Time ago in Spanish
 */
function timeAgo($datetime) {
    $timestamp = is_string($datetime) ? strtotime($datetime) : $datetime;
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'hace unos segundos';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return 'hace ' . $mins . ' minuto' . ($mins > 1 ? 's' : '');
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return 'hace ' . $hours . ' hora' . ($hours > 1 ? 's' : '');
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return 'hace ' . $days . ' día' . ($days > 1 ? 's' : '');
    } else {
        return formatDate($datetime);
    }
}

/**
 * Get status badge class
 */
function getStatusBadge($status) {
    $badges = [
        'abierto' => 'bg-blue-100 text-blue-800',
        'en_proceso' => 'bg-yellow-100 text-yellow-800',
        'en_espera_cliente' => 'bg-purple-100 text-purple-800',
        'resuelto' => 'bg-green-100 text-green-800',
        'cerrado' => 'bg-gray-100 text-gray-800'
    ];
    return $badges[$status] ?? 'bg-gray-100 text-gray-800';
}

/**
 * Get status label in Spanish
 */
function getStatusLabel($status) {
    $labels = [
        'abierto' => 'Abierto',
        'en_proceso' => 'En Proceso',
        'en_espera_cliente' => 'Esperando Cliente',
        'resuelto' => 'Resuelto',
        'cerrado' => 'Cerrado'
    ];
    return $labels[$status] ?? $status;
}

/**
 * Get priority badge class
 */
function getPriorityBadge($priority) {
    $badges = [
        'baja' => 'bg-green-100 text-green-800',
        'media' => 'bg-yellow-100 text-yellow-800',
        'alta' => 'bg-orange-100 text-orange-800',
        'critica' => 'bg-red-100 text-red-800'
    ];
    return $badges[$priority] ?? 'bg-gray-100 text-gray-800';
}

/**
 * Get priority label in Spanish
 */
function getPriorityLabel($priority) {
    $labels = [
        'baja' => 'Baja',
        'media' => 'Media',
        'alta' => 'Alta',
        'critica' => 'Crítica'
    ];
    return $labels[$priority] ?? $priority;
}

/**
 * Upload file
 */
function uploadFile($file, $folder = 'tickets') {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Error al subir el archivo'];
    }
    
    // Validate file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'El archivo es demasiado grande (máximo 5MB)'];
    }
    
    // Validate file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, ALLOWED_FILE_TYPES)) {
        return ['success' => false, 'error' => 'Tipo de archivo no permitido'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $uploadPath = UPLOAD_PATH . $folder . '/';
    
    // Create folder if not exists
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }
    
    // Move file
    $destination = $uploadPath . $filename;
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return [
            'success' => true,
            'filename' => $filename,
            'original_filename' => $file['name'],
            'path' => $folder . '/' . $filename,
            'size' => $file['size'],
            'type' => $mimeType
        ];
    }
    
    return ['success' => false, 'error' => 'Error al guardar el archivo'];
}

/**
 * Send JSON response
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Log audit action
 */
function logAudit($action, $entityType, $entityId, $details = null) {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO audit_log (user_id, action, entity_type, entity_id, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $userId = $_SESSION['user_id'] ?? null;
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $detailsJson = is_array($details) ? json_encode($details) : $details;
        
        $stmt->execute([$userId, $action, $entityType, $entityId, $detailsJson, $ipAddress, $userAgent]);
    } catch (Exception $e) {
        // Silent fail for audit logs
        error_log("Audit log error: " . $e->getMessage());
    }
}

/**
 * Create notification
 */
function createNotification($userId, $type, $message, $ticketId = null) {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO notifications (user_id, ticket_id, type, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $ticketId, $type, $message]);
        return true;
    } catch (Exception $e) {
        error_log("Notification error: " . $e->getMessage());
        return false;
    }
}

/**
 * Escape output
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Get asset URL
 */
function asset($path) {
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Get URL
 */
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}
