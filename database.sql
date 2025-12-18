-- Sistema de Soporte Técnico - Database Schema
-- MySQL 5.7 Compatible

-- Create Database
CREATE DATABASE IF NOT EXISTS soporte_tecnico CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE soporte_tecnico;

-- Table: users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(255) NOT NULL,
    whatsapp VARCHAR(20) UNIQUE NOT NULL,
    role ENUM('user', 'agent', 'admin') DEFAULT 'user',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_whatsapp (whatsapp),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: sessions
CREATE TABLE sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: tickets
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    agent_id INT NULL,
    category_id INT NOT NULL,
    asunto VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    prioridad ENUM('baja', 'media', 'alta', 'critica') DEFAULT 'media',
    estado ENUM('abierto', 'en_proceso', 'en_espera_cliente', 'resuelto', 'cerrado') DEFAULT 'abierto',
    sla_hours INT DEFAULT 24,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    closed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    INDEX idx_ticket_id (ticket_id),
    INDEX idx_user_id (user_id),
    INDEX idx_agent_id (agent_id),
    INDEX idx_estado (estado),
    INDEX idx_prioridad (prioridad),
    INDEX idx_category_id (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: ticket_messages
CREATE TABLE ticket_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    user_id INT NOT NULL,
    mensaje TEXT NOT NULL,
    is_internal TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_ticket_id (ticket_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: ticket_attachments
CREATE TABLE ticket_attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    message_id INT NULL,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    file_size INT NOT NULL,
    uploaded_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (message_id) REFERENCES ticket_messages(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_ticket_id (ticket_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: faqs
CREATE TABLE faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NULL,
    pregunta TEXT NOT NULL,
    respuesta TEXT NOT NULL,
    keywords TEXT,
    is_active TINYINT(1) DEFAULT 1,
    views INT DEFAULT 0,
    helpful_count INT DEFAULT 0,
    not_helpful_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_category_id (category_id),
    FULLTEXT idx_search (pregunta, respuesta, keywords)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: chatbot_conversations
CREATE TABLE chatbot_conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_id VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    response TEXT,
    faq_id INT NULL,
    created_ticket_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (faq_id) REFERENCES faqs(id) ON DELETE SET NULL,
    FOREIGN KEY (created_ticket_id) REFERENCES tickets(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_session_id (session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: audit_log
CREATE TABLE audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id INT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: system_settings
CREATE TABLE system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type VARCHAR(50) DEFAULT 'text',
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: notifications
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    ticket_id INT NULL,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    sent_whatsapp TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data for Querétaro, Mexico

-- Insert Admin User
INSERT INTO users (nombre_completo, whatsapp, role) VALUES
('Administrador Sistema', '+524421234567', 'admin');

-- Insert Agent Users
INSERT INTO users (nombre_completo, whatsapp, role) VALUES
('Juan Pérez García', '+524421234568', 'agent'),
('María López Hernández', '+524421234569', 'agent');

-- Insert Regular Users (Querétaro)
INSERT INTO users (nombre_completo, whatsapp, role) VALUES
('Carlos Ramírez Torres', '+524422345678', 'user'),
('Ana Martínez Sánchez', '+524422345679', 'user'),
('Luis Fernando Castro', '+524422345680', 'user'),
('Patricia Morales Díaz', '+524422345681', 'user'),
('Roberto González López', '+524422345682', 'user');

-- Insert Categories
INSERT INTO categories (nombre, descripcion) VALUES
('Técnico', 'Problemas técnicos con productos o servicios'),
('Facturación', 'Consultas sobre facturas y pagos'),
('Garantías', 'Solicitudes de garantía y devoluciones'),
('Instalación', 'Asistencia con instalación de productos'),
('Configuración', 'Ayuda con configuración de equipos'),
('Otros', 'Otras consultas generales');

-- Insert FAQs
INSERT INTO faqs (category_id, pregunta, respuesta, keywords, helpful_count) VALUES
(1, '¿Cómo reinicio mi equipo?', 'Para reiniciar su equipo, presione el botón de encendido durante 3 segundos o vaya a Inicio > Apagar > Reiniciar.', 'reinicio, reiniciar, resetear, equipo, computadora', 15),
(1, '¿Qué hago si mi equipo no enciende?', 'Verifique que el cable de alimentación esté conectado correctamente. Si el problema persiste, contacte a soporte técnico.', 'no enciende, no prende, apagado, energia', 22),
(2, '¿Cómo solicito una factura?', 'Puede solicitar su factura enviando su RFC y domicilio fiscal a facturacion@soporte.com dentro de los 30 días posteriores a su compra.', 'factura, cfdi, rfc, fiscal', 30),
(2, '¿Cuánto tiempo tarda en llegar mi factura?', 'Las facturas se generan en un plazo de 3 a 5 días hábiles después de recibir su solicitud.', 'factura, tiempo, plazo, dias', 18),
(3, '¿Cuál es el periodo de garantía?', 'Nuestros productos cuentan con garantía de 1 año contra defectos de fabricación a partir de la fecha de compra.', 'garantia, periodo, tiempo, año', 25),
(3, '¿Cómo hago válida mi garantía?', 'Para hacer válida su garantía, debe presentar su ticket de compra y el producto en cualquiera de nuestras sucursales en Querétaro.', 'garantia, valida, sucursal, ticket', 20),
(4, '¿Ofrecen servicio de instalación a domicilio?', 'Sí, ofrecemos servicio de instalación a domicilio en toda la zona metropolitana de Querétaro. El costo varía según el producto.', 'instalacion, domicilio, servicio, costo', 12),
(5, '¿Cómo configuro mi router?', 'Para configurar su router, conecte el cable ethernet a su modem, acceda a 192.168.1.1 desde su navegador y siga las instrucciones del asistente.', 'router, configurar, wifi, internet', 28),
(6, '¿Cuáles son los horarios de atención?', 'Nuestro horario de atención es de Lunes a Viernes de 9:00 AM a 6:00 PM y Sábados de 9:00 AM a 2:00 PM. Horario de Querétaro.', 'horario, atencion, abierto, cerrado', 35);

-- Insert Sample Tickets
INSERT INTO tickets (ticket_id, user_id, agent_id, category_id, asunto, descripcion, prioridad, estado) VALUES
('TKT-001', 4, 2, 1, 'Equipo no enciende después de actualización', 'Después de realizar una actualización del sistema, mi computadora no enciende. Solo muestra una pantalla negra.', 'alta', 'en_proceso'),
('TKT-002', 5, 2, 2, 'Solicitud de factura del mes pasado', 'Necesito la factura de mi compra realizada el mes pasado. Mi RFC es: XAXX010101000', 'media', 'resuelto'),
('TKT-003', 6, 3, 3, 'Producto defectuoso - Solicito cambio', 'El producto que compré la semana pasada presenta fallas. Solicito cambio por garantía.', 'alta', 'abierto'),
('TKT-004', 7, NULL, 4, 'Ayuda con instalación de impresora', 'Necesito ayuda para instalar mi nueva impresora. No encuentro los drivers.', 'baja', 'abierto'),
('TKT-005', 8, 3, 1, 'Internet muy lento', 'Mi conexión a internet está muy lenta desde hace dos días. Ya reinicié el modem.', 'media', 'en_espera_cliente');

-- Insert Sample Messages for Tickets
INSERT INTO ticket_messages (ticket_id, user_id, mensaje) VALUES
(1, 4, 'Después de realizar una actualización del sistema, mi computadora no enciende. Solo muestra una pantalla negra.'),
(1, 2, 'Gracias por contactarnos. ¿Puede intentar mantener presionado el botón de encendido por 10 segundos y luego encender de nuevo?'),
(1, 4, 'Ya lo intenté pero sigue igual. ¿Qué más puedo hacer?'),
(2, 5, 'Necesito la factura de mi compra realizada el mes pasado. Mi RFC es: XAXX010101000'),
(2, 2, 'Con gusto le ayudamos. ¿Puede proporcionarnos también su domicilio fiscal completo?'),
(2, 5, 'Claro, mi dirección es: Calle Juárez 123, Col. Centro, Querétaro, Qro. CP 76000'),
(2, 2, 'Perfecto. Su factura será enviada a su correo en los próximos 3 días hábiles.'),
(3, 6, 'El producto que compré la semana pasada presenta fallas. Solicito cambio por garantía.'),
(4, 7, 'Necesito ayuda para instalar mi nueva impresora. No encuentro los drivers.'),
(5, 8, 'Mi conexión a internet está muy lenta desde hace dos días. Ya reinicié el modem.'),
(5, 3, 'Gracias por reportar. ¿Puede hacer una prueba de velocidad en speedtest.net y compartir los resultados?');

-- Insert System Settings
INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES
('site_name', 'Sistema de Soporte Técnico Querétaro', 'text', 'Nombre del sitio'),
('site_logo', '', 'file', 'Logo del sitio'),
('primary_color', '#3B82F6', 'color', 'Color principal del sistema'),
('secondary_color', '#10B981', 'color', 'Color secundario del sistema'),
('contact_phone', '+52 442 123 4567', 'text', 'Teléfono de contacto principal'),
('contact_email', 'soporte@queretaro.com', 'email', 'Email de contacto'),
('whatsapp_business', '+52 442 123 4567', 'text', 'WhatsApp Business'),
('business_hours', '{"monday":{"start":"09:00","end":"18:00"},"tuesday":{"start":"09:00","end":"18:00"},"wednesday":{"start":"09:00","end":"18:00"},"thursday":{"start":"09:00","end":"18:00"},"friday":{"start":"09:00","end":"18:00"},"saturday":{"start":"09:00","end":"14:00"},"sunday":{"closed":true}}', 'json', 'Horarios de atención'),
('timezone', 'America/Mexico_City', 'text', 'Zona horaria del sistema'),
('auto_close_days', '7', 'number', 'Días para cerrar tickets automáticamente'),
('tickets_per_page', '10', 'number', 'Tickets por página'),
('enable_chatbot', '1', 'boolean', 'Habilitar chatbot'),
('paypal_client_id', '', 'text', 'PayPal Client ID'),
('paypal_mode', 'sandbox', 'select', 'PayPal Mode (sandbox/live)'),
('qr_api_key', '', 'text', 'API Key para generación de QR');

-- Insert Sample Audit Logs
INSERT INTO audit_log (user_id, action, entity_type, entity_id, details, ip_address) VALUES
(1, 'LOGIN', 'user', 1, 'Inicio de sesión exitoso', '192.168.1.100'),
(2, 'TICKET_ASSIGNED', 'ticket', 1, 'Ticket asignado al agente', '192.168.1.101'),
(4, 'TICKET_CREATED', 'ticket', 1, 'Nuevo ticket creado', '192.168.1.102'),
(2, 'TICKET_STATUS_CHANGED', 'ticket', 1, 'Estado cambiado a: en_proceso', '192.168.1.101'),
(5, 'TICKET_CREATED', 'ticket', 2, 'Nuevo ticket creado', '192.168.1.103');

-- Insert Sample Notifications
INSERT INTO notifications (user_id, ticket_id, type, message, is_read) VALUES
(4, 1, 'ticket_response', 'El agente ha respondido a su ticket TKT-001', 0),
(5, 2, 'ticket_resolved', 'Su ticket TKT-002 ha sido marcado como resuelto', 1),
(6, 3, 'ticket_created', 'Su ticket TKT-003 ha sido creado exitosamente', 0);

-- Create Views for reporting
CREATE OR REPLACE VIEW v_tickets_summary AS
SELECT 
    DATE(created_at) as fecha,
    COUNT(*) as total_tickets,
    SUM(CASE WHEN estado = 'abierto' THEN 1 ELSE 0 END) as abiertos,
    SUM(CASE WHEN estado = 'en_proceso' THEN 1 ELSE 0 END) as en_proceso,
    SUM(CASE WHEN estado = 'resuelto' THEN 1 ELSE 0 END) as resueltos,
    SUM(CASE WHEN estado = 'cerrado' THEN 1 ELSE 0 END) as cerrados
FROM tickets
GROUP BY DATE(created_at);

CREATE OR REPLACE VIEW v_agent_performance AS
SELECT 
    u.id,
    u.nombre_completo,
    COUNT(t.id) as total_tickets,
    SUM(CASE WHEN t.estado = 'resuelto' OR t.estado = 'cerrado' THEN 1 ELSE 0 END) as tickets_resueltos,
    AVG(TIMESTAMPDIFF(HOUR, t.created_at, t.updated_at)) as avg_response_time_hours
FROM users u
LEFT JOIN tickets t ON u.id = t.agent_id
WHERE u.role = 'agent'
GROUP BY u.id, u.nombre_completo;

-- Indexes for performance optimization
CREATE INDEX idx_tickets_created_at ON tickets(created_at);
CREATE INDEX idx_tickets_last_activity ON tickets(last_activity);
CREATE INDEX idx_messages_created_at ON ticket_messages(created_at);
