<?php
/**
 * Configuration File - Technical Support System
 * Database credentials and system settings
 */

// Error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Timezone
date_default_timezone_set('America/Mexico_City');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'soporte_tecnico');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Auto-detect BASE_URL
function detectBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $directory = str_replace('\\', '/', dirname($script));
    $directory = ($directory === '/') ? '' : $directory;
    return $protocol . "://" . $host . $directory;
}

define('BASE_URL', detectBaseUrl());
define('SITE_NAME', 'Sistema de Soporte Técnico');

// Session Configuration
define('SESSION_LIFETIME', 3600 * 24); // 24 hours
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);

// File Upload Configuration
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'text/plain']);
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');

// JWT Configuration (simple token generation)
define('JWT_SECRET', 'your-secret-key-change-in-production-' . md5(BASE_URL));

// WhatsApp API Configuration (prepared for future integration)
define('WHATSAPP_API_ENABLED', false);
define('WHATSAPP_API_KEY', '');
define('WHATSAPP_API_URL', '');

// Email Configuration
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USER', 'soporte@example.com');
define('SMTP_PASS', '');
define('SMTP_FROM', 'soporte@example.com');
define('SMTP_FROM_NAME', SITE_NAME);

// PayPal Configuration
define('PAYPAL_CLIENT_ID', '');
define('PAYPAL_SECRET', '');
define('PAYPAL_MODE', 'sandbox'); // sandbox or live

// Business Hours
define('BUSINESS_HOURS', json_encode([
    'monday' => ['start' => '09:00', 'end' => '18:00'],
    'tuesday' => ['start' => '09:00', 'end' => '18:00'],
    'wednesday' => ['start' => '09:00', 'end' => '18:00'],
    'thursday' => ['start' => '09:00', 'end' => '18:00'],
    'friday' => ['start' => '09:00', 'end' => '18:00'],
    'saturday' => ['start' => '09:00', 'end' => '14:00'],
    'sunday' => ['closed' => true]
]));

// SLA Configuration (in hours)
define('SLA_CRITICAL', 1);
define('SLA_HIGH', 4);
define('SLA_MEDIUM', 24);
define('SLA_LOW', 48);

// Auto-close tickets after X days of inactivity
define('AUTO_CLOSE_DAYS', 7);

// System Roles
define('ROLE_USER', 'user');
define('ROLE_AGENT', 'agent');
define('ROLE_ADMIN', 'admin');

// Ticket Status
define('TICKET_STATUS_OPEN', 'abierto');
define('TICKET_STATUS_IN_PROGRESS', 'en_proceso');
define('TICKET_STATUS_WAITING_CLIENT', 'en_espera_cliente');
define('TICKET_STATUS_RESOLVED', 'resuelto');
define('TICKET_STATUS_CLOSED', 'cerrado');

// Ticket Priorities
define('TICKET_PRIORITY_LOW', 'baja');
define('TICKET_PRIORITY_MEDIUM', 'media');
define('TICKET_PRIORITY_HIGH', 'alta');
define('TICKET_PRIORITY_CRITICAL', 'critica');

// QR API Configuration
define('QR_API_KEY', '');
define('QR_API_URL', '');

// System version
define('SYSTEM_VERSION', '1.0.0');
