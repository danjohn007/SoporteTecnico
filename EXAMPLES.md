# Ejemplos de Uso - Sistema Corregido

Este documento muestra cómo funcionan las correcciones implementadas.

## Ejemplo 1: Flujo de Rutas con .htaccess Corregido

### Antes (Con el Bug):
```
Usuario solicita: http://midominio.com/tickets/create
   ↓
.htaccess raíz: RewriteRule ^(.*)$ public/$1 [L]
   ↓
Redirección a: http://midominio.com/public/tickets/create
   ↓
.htaccess raíz: RewriteRule ^(.*)$ public/index.php?url=$1 [QSA,L]
   ↓  (Esta regla NUNCA se ejecuta porque la anterior tiene [L])
RESULTADO: 404 o comportamiento inesperado
```

### Después (Corregido):
```
Usuario solicita: http://midominio.com/tickets/create
   ↓
.htaccess raíz: 
   - Verifica que NO esté en /public/
   - Verifica que NO sea archivo/directorio existente
   - RewriteRule ^(.*)$ public/$1 [L]
   ↓
Redirección a: http://midominio.com/public/tickets/create
   ↓
.htaccess en public/:
   - Verifica que NO sea archivo/directorio existente
   - RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
   ↓
PHP procesa: index.php?url=tickets/create
   ↓
Controlador: TicketsController->create()
   ↓
RESULTADO: ✅ Página funciona correctamente
```

## Ejemplo 2: Manejo de Errores de Base de Datos

### Antes (Con el Bug):
```php
// En config/database.php
try {
    $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch(PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
```

**Problema:**
- Muestra error críptico en pantalla
- Expone detalles técnicos al usuario
- HTTP 500 genérico
- No se registra el error

**Resultado visible:**
```
Database Connection Failed: SQLSTATE[HY000] [1045] Access denied for user 'arosport_soporte'@'localhost' (using password: YES)
```

### Después (Corregido):
```php
// En config/database.php
try {
    $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch(PDOException $e) {
    // 1. Registrar el error
    error_log("Database Connection Failed: " . $e->getMessage());
    
    // 2. Establecer código HTTP apropiado
    http_response_code(503);
    
    // 3. Preparar mensajes escapados
    $errorMessage = htmlspecialchars("Error de Conexión a la Base de Datos");
    $errorDetails = htmlspecialchars("No se pudo conectar...");
    
    // 4. En desarrollo, registrar detalles adicionales
    if (getenv('APP_ENV') === 'development') {
        error_log("Database Error Details: " . $e->getMessage());
    }
    
    // 5. Mostrar página de error profesional
    echo '<!DOCTYPE html>...';
    exit();
}
```

**Resultado visible:**
```
⚠️
Error de Conexión a la Base de Datos
No se pudo conectar a la base de datos. 
Por favor, contacte al administrador del sistema.
```

**En el log (logs/php-error.log):**
```
[18-Dec-2024 19:20:00 UTC] Database Connection Failed: SQLSTATE[HY000] [1045] Access denied...
[18-Dec-2024 19:20:00 UTC] Database Error Details: SQLSTATE[HY000] [1045] Access denied...
```

## Ejemplo 3: Generación de URLs

### Antes (Con el Bug):

```php
// En config/config.php
function detectBaseUrl() {
    $protocol = "http";
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME']; // "/public/index.php"
    $directory = dirname($script);      // "/public"
    return $protocol . "://" . $host . $directory;
}
// Resultado: http://midominio.com/public

// En app/helpers.php
function asset($path) {
    return BASE_URL . '/public/' . ltrim($path, '/');
}
// Resultado: http://midominio.com/public/public/css/style.css ❌
```

**Problema:** URLs duplicadas, CSS/JS no cargan

### Después (Corregido):

```php
// En config/config.php
function detectBaseUrl() {
    $protocol = "http";
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME']; // "/public/index.php"
    $directory = dirname($script);      // "/public"
    
    // Remover /public del path
    $directory = str_replace('/public', '', $directory);
    
    return $protocol . "://" . $host . $directory;
}
// Resultado: http://midominio.com ✅

// En app/helpers.php
function asset($path) {
    return BASE_URL . '/' . ltrim($path, '/');
}
// Resultado: http://midominio.com/css/style.css ✅
```

**Flujo completo:**
```
Vista solicita: asset('css/style.css')
   ↓
Genera URL: http://midominio.com/css/style.css
   ↓
Browser solicita esa URL
   ↓
.htaccess raíz redirige a: public/css/style.css
   ↓
Apache encuentra el archivo y lo sirve
   ↓
CSS carga correctamente ✅
```

## Ejemplo 4: Acceso a Rutas Protegidas

### Usuario NO autenticado intenta acceder a /tickets/create

```php
// En TicketsController.php
public function __construct() {
    requireLogin();  // ← Verifica autenticación
    $this->ticketModel = $this->model('Ticket');
    $this->categoryModel = $this->model('Category');
}

// En app/helpers.php
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect('auth/login');  // ← Redirección antes de tocar BD
    }
}
```

**Flujo:**
```
1. Usuario solicita: /tickets/create
2. TicketsController se instancia
3. Constructor llama requireLogin()
4. requireLogin() detecta que no hay sesión
5. Guarda la URL solicitada en sesión
6. Redirige a: /auth/login
7. Usuario ve página de login ✅
8. Después de login, regresa a /tickets/create
```

**Importante:** La redirección ocurre ANTES de intentar conectar a la base de datos, por lo que no hay errores.

### Usuario autenticado accede a /faq

```php
// En FaqController.php
public function __construct() {
    // NO llama requireLogin() - FAQs son públicas
    $this->faqModel = $this->model('FAQ');
    $this->categoryModel = $this->model('Category');
}
```

**Flujo:**
```
1. Usuario solicita: /faq
2. FaqController se instancia
3. Constructor NO requiere login ✅
4. Conecta a base de datos para obtener FAQs
5. Muestra la vista faq/index.php
6. Página se muestra correctamente
```

## Ejemplo 5: Estructura de Archivos Corregida

### Antes:
```
SoporteTecnico/
├── .htaccess
├── public/
│   ├── index.php
│   └── .htaccess
├── config/
├── app/
└── ... (sin logs/ ni uploads/)
```

**Problemas:**
- Errores no se registran
- No hay donde guardar archivos adjuntos
- Sistema falla al intentar escribir

### Después:
```
SoporteTecnico/
├── .htaccess (corregido)
├── logs/
│   └── .gitkeep
├── public/
│   ├── index.php
│   ├── .htaccess
│   └── uploads/
│       ├── .gitkeep
│       └── tickets/
│           └── .gitkeep
├── config/ (mejorado)
├── app/ (corregido)
├── TROUBLESHOOTING.md (nuevo)
└── FIXES_SUMMARY.md (nuevo)
```

**Beneficios:**
✅ Errores se registran en logs/php-error.log
✅ Archivos se guardan en public/uploads/tickets/
✅ Sistema funciona completamente
✅ Documentación completa disponible

## Verificación del Sistema

### 1. Verificar que .htaccess funciona:

```bash
# Probar ruta principal
curl -I http://midominio.com/
# Debe retornar: HTTP/1.1 200 OK

# Probar ruta de controlador
curl -I http://midominio.com/faq
# Debe retornar: HTTP/1.1 200 OK (o 302 si requiere login)

# Probar archivo estático
curl -I http://midominio.com/css/style.css
# Debe retornar: HTTP/1.1 200 OK
```

### 2. Verificar manejo de errores:

```bash
# Temporalmente apagar MySQL
sudo service mysql stop

# Intentar acceder al sistema
curl http://midominio.com/faq

# Debe mostrar:
# - Código HTTP 503
# - Página de error profesional
# - NO debe mostrar detalles técnicos

# Verificar log
tail -f logs/php-error.log
# Debe mostrar: Database Connection Failed: ...

# Reiniciar MySQL
sudo service mysql start
```

### 3. Verificar generación de URLs:

```bash
# Ver el HTML generado
curl http://midominio.com/ | grep -i "href\|src"

# Debe mostrar URLs como:
# href="http://midominio.com/auth/login"
# src="http://midominio.com/css/style.css"
#
# NO debe mostrar:
# href="http://midominio.com/public/auth/login" ❌
# src="http://midominio.com/public/public/css/style.css" ❌
```

## Resumen de Mejoras

| Aspecto | Antes | Después |
|---------|-------|---------|
| .htaccess | Reglas conflictivas | Simplificado y funcional |
| Errores BD | die() con mensaje crudo | Página 503 profesional |
| Logs | No se registran | Se registran en logs/ |
| URLs | Duplicadas con /public/ | Correctas y limpias |
| Seguridad | Credenciales expuestas | Placeholders y sanitización |
| Assets | No cargan | Cargan correctamente |
| Directorios | Faltantes | Completos con .gitkeep |
| Documentación | Mínima | Completa y detallada |

## Conclusión

Todas las correcciones trabajan juntas para:
1. ✅ Permitir el acceso al sistema
2. ✅ Eliminar los errores HTTP 500
3. ✅ Proporcionar mensajes de error útiles
4. ✅ Mantener la seguridad del sistema
5. ✅ Facilitar el mantenimiento futuro

El sistema ahora está listo para producción con todas las mejores prácticas implementadas.
