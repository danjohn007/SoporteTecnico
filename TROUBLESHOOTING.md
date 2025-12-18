# Guía de Solución de Problemas - Sistema de Soporte Técnico

## Problemas Resueltos

### 1. Error con .htaccess que no permitía entrar al sistema

**Problema:** El archivo `.htaccess` en la raíz tenía reglas de reescritura conflictivas que impedían el acceso correcto al sistema.

**Solución Implementada:**
- Se simplificó el `.htaccess` raíz para eliminar reglas redundantes
- Se corrigió el flujo de redirección para que funcione correctamente:
  1. Todas las peticiones se redirigen a la carpeta `public/`
  2. El `.htaccess` en `public/` maneja el enrutamiento a través de `index.php`

**Archivo modificado:** `.htaccess`

### 2. HTTP ERROR 500 en /public/tickets, /public/tickets/create, /public/faq

**Problema:** Errores HTTP 500 causados por varios factores:
1. Manejo inadecuado de errores de conexión a la base de datos
2. Detección incorrecta de BASE_URL que incluía `/public/`
3. Función `asset()` generaba URLs incorrectas

**Soluciones Implementadas:**

#### a) Mejora en el manejo de errores de base de datos
- Se reemplazó `die()` con manejo de errores más robusto
- Se muestra una página de error amigable (HTTP 503) cuando falla la conexión
- Los errores se registran en el log del sistema
- En modo desarrollo, se muestran detalles técnicos

**Archivo modificado:** `config/database.php`

#### b) Corrección de BASE_URL
- La función `detectBaseUrl()` ahora excluye correctamente `/public/` del URL base
- Esto asegura que todas las rutas y redirecciones funcionen correctamente

**Archivo modificado:** `config/config.php`

#### c) Corrección de función asset()
- La función `asset()` ahora genera URLs correctas que funcionan con las redirecciones .htaccess
- Ya no agrega `/public/` duplicado a las rutas de assets

**Archivo modificado:** `app/helpers.php`

#### d) Estructura de directorios
- Se creó el directorio `logs/` para registro de errores
- Se creó el directorio `public/uploads/` para archivos subidos

## Requisitos del Sistema

Para que el sistema funcione correctamente, asegúrate de:

### 1. Apache con mod_rewrite habilitado

```bash
# En Ubuntu/Debian
sudo a2enmod rewrite
sudo service apache2 restart

# Verificar que esté habilitado
apache2ctl -M | grep rewrite
```

### 2. MySQL Server corriendo

```bash
# Verificar que MySQL esté corriendo
sudo service mysql status

# Iniciar MySQL si no está corriendo
sudo service mysql start
```

### 3. Base de datos configurada

1. Crear la base de datos:
```sql
CREATE DATABASE arosport_soporte CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Importar el esquema:
```bash
mysql -u root -p arosport_soporte < database.sql
```

3. Verificar credenciales en `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'arosport_soporte');
define('DB_USER', 'arosport_soporte');
define('DB_PASS', 'Danjohn007!');
```

### 4. Permisos de archivos

```bash
# Permisos para el directorio de uploads
chmod -R 755 public/uploads/
chown -R www-data:www-data public/uploads/

# Permisos para el directorio de logs
chmod -R 755 logs/
chown -R www-data:www-data logs/
```

### 5. Configuración de Apache

Asegúrate de que tu configuración de Apache permita .htaccess:

```apache
<Directory /var/www/html/tu-proyecto>
    AllowOverride All
    Require all granted
</Directory>
```

## Verificación de la Instalación

### 1. Probar conexión a base de datos

Accede a: `http://tu-dominio/test_connection.php`

### 2. Probar rutas

- Página principal: `http://tu-dominio/`
- Login: `http://tu-dominio/auth/login`
- FAQ: `http://tu-dominio/faq`
- Crear ticket (requiere login): `http://tu-dominio/tickets/create`
- Ver tickets (requiere login): `http://tu-dominio/tickets`

## Modo de Desarrollo vs Producción

### Activar modo desarrollo

Para ver errores detallados durante el desarrollo:

```bash
export APP_ENV=development
```

O en tu configuración de Apache:
```apache
SetEnv APP_ENV development
```

### Modo producción (recomendado)

Por defecto, el sistema está en modo producción:
- Los errores no se muestran en pantalla
- Los errores se registran en `logs/php-error.log`
- Se muestra una página de error amigable al usuario

## Solución de Problemas Comunes

### Error: "Database Connection Failed"

**Causas posibles:**
1. MySQL no está corriendo
2. Las credenciales en `config/config.php` son incorrectas
3. La base de datos no existe
4. El usuario no tiene permisos

**Solución:**
```bash
# Verificar MySQL
sudo service mysql status

# Verificar credenciales
mysql -h localhost -u arosport_soporte -p
# Ingresar password: Danjohn007!

# Verificar base de datos
mysql -u root -p -e "SHOW DATABASES LIKE 'arosport_soporte';"
```

### Error: "No se pueden cargar los estilos CSS/JS"

**Causa:** BASE_URL mal configurado

**Solución:** El sistema ahora detecta automáticamente el BASE_URL. Si aún hay problemas, verifica:
1. Que mod_rewrite esté habilitado
2. Que .htaccess tenga los permisos correctos (644)

### Error 404 en todas las rutas

**Causa:** mod_rewrite no está habilitado o AllowOverride no está configurado

**Solución:**
```bash
sudo a2enmod rewrite
sudo service apache2 restart
```

Y verifica que tu VirtualHost tenga:
```apache
AllowOverride All
```

## Estructura de URLs

Con las correcciones implementadas, el sistema usa las siguientes URLs:

```
http://tu-dominio/                    → Página principal
http://tu-dominio/auth/login          → Login
http://tu-dominio/auth/register       → Registro
http://tu-dominio/dashboard           → Dashboard
http://tu-dominio/tickets             → Lista de tickets
http://tu-dominio/tickets/create      → Crear ticket
http://tu-dominio/tickets/view/1      → Ver ticket #1
http://tu-dominio/faq                 → Preguntas frecuentes
http://tu-dominio/chatbot             → Chatbot
http://tu-dominio/admin               → Panel de administración
```

## Registro de Cambios

### Versión 1.0.1 - 2024-12-18

- ✅ Corregido .htaccess con reglas de reescritura conflictivas
- ✅ Mejorado manejo de errores de conexión a base de datos
- ✅ Corregida detección automática de BASE_URL
- ✅ Corregida función asset() para generar URLs correctas
- ✅ Creados directorios necesarios (logs/, uploads/)
- ✅ Agregada documentación de solución de problemas

## Soporte

Si continúas teniendo problemas después de seguir esta guía:

1. Verifica los logs en `logs/php-error.log`
2. Verifica los logs de Apache en `/var/log/apache2/error.log`
3. Activa el modo desarrollo para ver errores detallados
4. Abre un issue en el repositorio de GitHub
