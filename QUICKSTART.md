# Guía Rápida de Inicio - Sistema de Soporte Técnico

## 🚀 Instalación Rápida (5 minutos)

### Paso 1: Requisitos Previos
```bash
# Verifica que tienes:
php --version    # PHP 7.0+
mysql --version  # MySQL 5.7+
apache2 -v       # Apache 2.4+
```

### Paso 2: Clonar o Descargar
```bash
git clone https://github.com/danjohn007/SoporteTecnico.git
cd SoporteTecnico
```

### Paso 3: Configurar Base de Datos
```bash
# Crear base de datos
mysql -u root -p -e "CREATE DATABASE soporte_tecnico CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Importar esquema
mysql -u root -p soporte_tecnico < database.sql
```

### Paso 4: Configurar Credenciales
Edita `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'soporte_tecnico');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_password');
```

### Paso 5: Configurar Permisos
```bash
chmod -R 755 public/uploads/
chown -R www-data:www-data public/uploads/
```

### Paso 6: Habilitar mod_rewrite
```bash
# Ubuntu/Debian
sudo a2enmod rewrite
sudo service apache2 restart
```

### Paso 7: Probar Instalación
Visita: `http://localhost/SoporteTecnico/test_connection.php`

---

## 👤 Usuarios de Prueba

### Administrador
- **WhatsApp:** +524421234567
- **Acceso:** Login sin contraseña

### Agentes
- **WhatsApp:** +524421234568
- **WhatsApp:** +524421234569

### Clientes de Prueba
- +524422345678
- +524422345679
- +524422345680
- +524422345681
- +524422345682

---

## 📋 Primeros Pasos

### Como Usuario (Cliente)

1. **Registrarse**
   - Ve a `/auth/register`
   - Ingresa tu nombre y WhatsApp
   - ¡Listo! No necesitas contraseña

2. **Buscar Respuestas**
   - Usa el **Chatbot** para preguntas rápidas
   - Consulta las **FAQs**
   
3. **Crear un Ticket**
   - Dashboard → "Crear Ticket"
   - Selecciona categoría y prioridad
   - Describe tu problema
   - Adjunta archivos si es necesario

4. **Seguimiento**
   - Ve el estado en tiempo real
   - Responde mensajes del agente
   - Recibe notificaciones

### Como Agente

1. **Ver Tickets Asignados**
   - Dashboard muestra tus tickets
   - Filtra por estado/prioridad

2. **Asignar Tickets**
   - Click en "Asignarme" en tickets sin asignar
   - O asigna a otro agente

3. **Responder**
   - Abre el ticket
   - Escribe tu respuesta
   - Adjunta archivos si necesario
   - Cambia el estado

4. **Cambiar Estados**
   - Abierto → En Proceso
   - En Proceso → En Espera de Cliente
   - En Espera → Resuelto
   - Resuelto → Cerrado

### Como Administrador

1. **Gestionar Usuarios**
   - Admin → Usuarios
   - Cambiar roles (Usuario/Agente/Admin)
   - Desactivar usuarios

2. **Categorías**
   - Admin → Categorías
   - Crear, editar, eliminar

3. **FAQs**
   - Admin → FAQs
   - Agregar preguntas y respuestas
   - Incluir keywords para búsqueda

4. **Configuraciones**
   - Settings → Configuraciones
   - Personalizar nombre y logo
   - Colores del tema
   - Integraciones

5. **Reportes**
   - Admin → Reportes
   - Métricas de tickets
   - Desempeño de agentes

---

## 🎯 Casos de Uso Comunes

### Flujo Típico de Ticket

```
1. Cliente busca en FAQ/Chatbot
2. Si no encuentra respuesta → Crea Ticket
3. Sistema notifica (prioridad, SLA)
4. Agente se asigna el ticket
5. Agente investiga y responde
6. Cliente proporciona más información
7. Agente resuelve el problema
8. Cliente confirma → Ticket cerrado
```

### Chatbot Inteligente

```
Usuario: "¿Cómo reinicio mi router?"
Chatbot: Busca en FAQs → Muestra respuesta
Usuario: "¿Fue útil?" → Sí/No
Si no fue útil → Ofrece crear ticket
```

### Auto-Cierre de Tickets

```
Ticket en estado "Resuelto"
+ 7 días sin actividad
= Auto-cierre automático
```

---

## 🔧 Configuraciones Importantes

### Horarios de Atención
Edita en Settings o `config/config.php`:
```php
define('BUSINESS_HOURS', json_encode([
    'monday' => ['start' => '09:00', 'end' => '18:00'],
    // ... resto de días
]));
```

### SLA por Prioridad
```php
define('SLA_CRITICAL', 1);   // 1 hora
define('SLA_HIGH', 4);       // 4 horas
define('SLA_MEDIUM', 24);    // 24 horas
define('SLA_LOW', 48);       // 48 horas
```

### Auto-Cierre
```php
define('AUTO_CLOSE_DAYS', 7); // 7 días
```

### Límites de Archivos
```php
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', [
    'image/jpeg', 'image/png', 'image/gif',
    'application/pdf', 'text/plain'
]);
```

---

## 🎨 Personalización

### Cambiar Colores
Settings → Apariencia:
- Color Principal (Primary)
- Color Secundario (Secondary)

O edita `config/config.php` y actualiza Tailwind config.

### Logo del Sistema
Settings → General → Subir Logo

### Nombre del Sistema
```php
define('SITE_NAME', 'Tu Nombre Aquí');
```

---

## 🔒 Seguridad

### Checklist de Seguridad
- [x] PDO con Prepared Statements
- [x] Sanitización de inputs
- [x] Validación de archivos
- [x] Tokens de sesión únicos
- [x] Headers de seguridad HTTP
- [x] Protección de archivos sensibles
- [ ] HTTPS en producción (recomendado)
- [ ] Rate limiting (recomendado)

### Cambiar Secret Key
```php
define('JWT_SECRET', 'tu-clave-secreta-única');
```

---

## 📊 Monitoreo y Mantenimiento

### Ver Logs de Auditoría
```sql
SELECT * FROM audit_log ORDER BY created_at DESC LIMIT 100;
```

### Ver Estadísticas
```sql
SELECT * FROM v_tickets_summary;
SELECT * FROM v_agent_performance;
```

### Limpiar Sesiones Expiradas
```php
// Ejecutar periódicamente vía cron
$session = new Session();
$session->cleanExpired();
```

### Auto-Cerrar Tickets Inactivos
```php
// Ejecutar diariamente vía cron
$ticket = new Ticket();
$ticket->autoCloseInactive();
```

---

## 🐛 Solución Rápida de Problemas

### Problema: Error 500
**Solución:**
```bash
# Verificar mod_rewrite
sudo a2enmod rewrite
sudo service apache2 restart

# Verificar permisos
chmod -R 755 public/uploads/
```

### Problema: Estilos No Cargan
**Solución:**
- Verifica que Tailwind CDN esté accesible
- Revisa la URL base en config.php
- Limpia caché del navegador

### Problema: Error Base de Datos
**Solución:**
- Verifica credenciales en config.php
- Asegura que MySQL esté corriendo
- Importa database.sql nuevamente

### Problema: Archivos No Suben
**Solución:**
```bash
# Verificar permisos
ls -la public/uploads/
chmod -R 755 public/uploads/

# Verificar tamaño máximo en PHP
php -i | grep upload_max_filesize
```

---

## 📱 Integración WhatsApp

### Twilio
```php
// En config.php
define('WHATSAPP_API_ENABLED', true);
define('WHATSAPP_API_KEY', 'tu_twilio_sid');
define('WHATSAPP_API_URL', 'https://api.twilio.com/...');
```

### Meta (Facebook)
```php
// Similar configuración para Meta Business API
```

---

## 🚀 Poner en Producción

### Checklist Pre-Producción
- [ ] Cambiar DB_USER y DB_PASS
- [ ] Cambiar JWT_SECRET
- [ ] Deshabilitar display_errors
- [ ] Configurar HTTPS
- [ ] Configurar backups automáticos
- [ ] Configurar emails SMTP
- [ ] Integrar WhatsApp API
- [ ] Configurar PayPal (si aplica)
- [ ] Rate limiting
- [ ] Monitoreo de errores

### Configurar HTTPS
```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d tudominio.com
```

### Backup Automático
```bash
# Agregar a crontab
0 2 * * * mysqldump -u user -p'pass' soporte_tecnico > /backups/db_$(date +\%Y\%m\%d).sql
```

---

## 📚 Recursos Adicionales

- **README.md**: Documentación completa
- **API.md**: Documentación de API
- **database.sql**: Esquema y datos de ejemplo
- **test_connection.php**: Test de configuración

---

## 🆘 Soporte

### Obtener Ayuda
- GitHub Issues: https://github.com/danjohn007/SoporteTecnico/issues
- Email: soporte@example.com

### Contribuir
1. Fork el proyecto
2. Crea tu feature branch
3. Commit tus cambios
4. Push y crea Pull Request

---

**¡Listo para empezar! 🎉**

Si completaste todos los pasos, tu sistema está funcionando.
Visita: `http://localhost/SoporteTecnico/`
