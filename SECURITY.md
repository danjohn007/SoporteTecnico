# Security Checklist - Sistema de Soporte Técnico

## 🔒 Configuraciones de Seguridad Pre-Producción

### Crítico - Debe Hacerse Antes de Producción

#### 1. Base de Datos
- [ ] **Cambiar credenciales de base de datos**
  - Crear usuario dedicado con privilegios limitados
  - Establecer contraseña fuerte
  - Editar `config/config.php`:
    ```php
    define('DB_USER', 'soporte_user'); // No usar root
    define('DB_PASS', 'strong_password_here');
    ```

#### 2. Claves Secretas
- [ ] **Cambiar JWT_SECRET**
  ```php
  define('JWT_SECRET', 'tu-clave-secreta-unica-de-64-caracteres-minimo');
  ```
  Generar con: `php -r "echo bin2hex(random_bytes(32));"`

#### 3. Variables de Entorno
- [ ] **Establecer APP_ENV**
  ```bash
  export APP_ENV=production
  ```
  O en Apache:
  ```apache
  SetEnv APP_ENV production
  ```

#### 4. Error Reporting
- [ ] Verificar que los errores NO se muestren en pantalla
- [ ] Configurar logs en: `logs/php-error.log`
- [ ] Establecer permisos adecuados en directorio logs:
  ```bash
  chmod 755 logs/
  chmod 644 logs/.gitkeep
  ```

#### 5. HTTPS
- [ ] Instalar certificado SSL (Let's Encrypt recomendado)
  ```bash
  sudo certbot --apache -d tudominio.com
  ```
- [ ] Forzar HTTPS en .htaccess:
  ```apache
  RewriteCond %{HTTPS} off
  RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
  ```

#### 6. Permisos de Archivos
```bash
# Archivos
find . -type f -exec chmod 644 {} \;

# Directorios
find . -type d -exec chmod 755 {} \;

# Uploads debe ser escribible por Apache
chmod -R 755 public/uploads/
chown -R www-data:www-data public/uploads/

# Logs debe ser escribible
chmod 755 logs/
chown -R www-data:www-data logs/

# Proteger archivos sensibles
chmod 600 config/config.php
```

### Importante - Recomendado para Producción

#### 7. Headers de Seguridad
Agregar a `.htaccess`:
```apache
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
Header set Strict-Transport-Security "max-age=31536000; includeSubDomains"
Header set Content-Security-Policy "default-src 'self'"
Header set Referrer-Policy "strict-origin-when-cross-origin"
```

#### 8. Rate Limiting
Considerar implementar límites de peticiones:
- 100 requests/minuto por IP para endpoints públicos
- 1000 requests/hora para API
- Usar fail2ban o mod_evasive

#### 9. Backups Automáticos
```bash
# Script de backup diario
#!/bin/bash
mysqldump -u usuario -p'password' soporte_tecnico | gzip > /backups/db_$(date +%Y%m%d).sql.gz

# Agregar a crontab
0 2 * * * /path/to/backup.sh
```

#### 10. Monitoreo de Logs
- [ ] Configurar rotación de logs
- [ ] Monitorear logs de errores
- [ ] Alertas para errores críticos

### Opcional - Mejoras de Seguridad Adicionales

#### 11. WAF (Web Application Firewall)
- ModSecurity para Apache
- Cloudflare (protección DDoS)

#### 12. Autenticación de Dos Factores
- Implementar 2FA para administradores
- Usar Google Authenticator o similar

#### 13. Auditoría de Dependencias
- Mantener PHP actualizado
- Revisar actualizaciones de seguridad

#### 14. Protección Contra CSRF
- Sistema ya implementado con `generateCsrfToken()`
- Verificar en todos los formularios

#### 15. Sanitización de Inputs
- Sistema ya implementado con `sanitize()`
- Usar en todos los inputs de usuario

## 🛡️ Características de Seguridad Ya Implementadas

✅ **PDO con Prepared Statements** - Previene SQL Injection
✅ **Sanitización de Inputs** - Función `sanitize()` global
✅ **Validación de Archivos** - Tipo y tamaño
✅ **Tokens de Sesión** - Únicos y seguros
✅ **CSRF Protection** - Sistema de tokens
✅ **XSS Prevention** - htmlspecialchars() en outputs
✅ **Control de Acceso Basado en Roles** - requireRole()
✅ **Auditoría de Acciones** - Tabla audit_log
✅ **Protección de Archivos Sensibles** - .htaccess
✅ **Validación de WhatsApp** - Formato correcto
✅ **Whitelist de Controllers** - Previene acceso no autorizado

## 🔍 Pruebas de Seguridad Recomendadas

### Antes de Producción
1. **Test de Penetración**
   - OWASP ZAP
   - Burp Suite Community

2. **Análisis de Vulnerabilidades**
   - Nikto
   - SQLMap (para verificar que NO haya SQL injection)

3. **Revisión de Código**
   - PHPStan
   - Psalm
   - SonarQube

4. **Test de Carga**
   - Apache Bench (ab)
   - JMeter

## 📋 Checklist Final

Antes de poner en producción, verificar:

- [ ] Credenciales de DB cambiadas
- [ ] JWT_SECRET único establecido
- [ ] APP_ENV=production configurado
- [ ] HTTPS habilitado y forzado
- [ ] Error reporting deshabilitado
- [ ] Logs configurados correctamente
- [ ] Permisos de archivos correctos
- [ ] Headers de seguridad agregados
- [ ] Backups automáticos configurados
- [ ] Pruebas de seguridad realizadas
- [ ] Monitoreo de logs activo
- [ ] Documentación actualizada
- [ ] Plan de respuesta a incidentes
- [ ] Información de contacto actualizada

## 🚨 En Caso de Incidente de Seguridad

1. **Aislar** - Desconectar del internet si es necesario
2. **Investigar** - Revisar logs de auditoría
3. **Remediar** - Cambiar credenciales, parchear vulnerabilidades
4. **Notificar** - Informar a usuarios afectados si aplica
5. **Documentar** - Registrar incidente y acciones tomadas
6. **Prevenir** - Implementar medidas para evitar recurrencia

## 📞 Contacto de Seguridad

Para reportar vulnerabilidades de seguridad:
- Email: security@example.com
- GitHub Security Advisories
- No divulgar públicamente hasta que se resuelva

---

**Última actualización:** 2024
**Versión:** 1.0.0

**Nota:** Esta lista se debe revisar y actualizar regularmente conforme
evolucionan las mejores prácticas de seguridad.
