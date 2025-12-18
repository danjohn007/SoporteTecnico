# Sistema Online de Soporte Técnico

Sistema integral de soporte técnico con gestión de tickets, chatbot FAQ inteligente, y panel de administración completo.

## 🌟 Características Principales

### Funcionalidades Generales
- ✅ Registro y autenticación sin contraseña (solo WhatsApp)
- ✅ Sistema de tickets con múltiples estados y prioridades
- ✅ Chatbot inteligente con base de conocimiento FAQ
- ✅ Panel de usuario, agente y administrador
- ✅ Gestión completa de categorías y FAQs
- ✅ Sistema de notificaciones
- ✅ Reportes y métricas detalladas
- ✅ Adjuntos en tickets (imágenes, PDFs, documentos)
- ✅ Historial de conversación en tickets
- ✅ Configuraciones personalizables del sistema
- ✅ Auditoría completa de acciones
- ✅ Control de roles (Usuario, Agente, Administrador)
- ✅ Diseño responsive con Tailwind CSS

### Roles del Sistema

#### 👤 Usuario (Cliente)
- Crear y gestionar tickets de soporte
- Ver historial de tickets
- Interactuar con el chatbot FAQ
- Recibir notificaciones de actualizaciones
- Adjuntar archivos a los tickets

#### 👨‍💼 Agente de Soporte
- Ver y gestionar tickets asignados
- Cambiar estado de tickets
- Asignar tickets a sí mismo o a otros agentes
- Responder a consultas de clientes
- Filtrar tickets por estado, prioridad y categoría

#### ⚙️ Administrador
- Todas las funciones de agente
- Gestión de usuarios (crear, editar, desactivar)
- Gestión de categorías de soporte
- Gestión de FAQs
- Configuraciones del sistema
- Reportes y métricas
- Ver logs de auditoría

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP 7.0+ (sin framework)
- **Base de Datos**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Estilos**: Tailwind CSS
- **Iconos**: Font Awesome 6
- **Arquitectura**: MVC (Model-View-Controller)

## 📋 Requisitos del Sistema

- PHP 7.0 o superior
- MySQL 5.7 o superior
- Apache 2.4+ con mod_rewrite habilitado
- Extensiones PHP requeridas:
  - PDO
  - pdo_mysql
  - json
  - mbstring
  - fileinfo

## 🚀 Instalación

### 1. Clonar o Descargar el Repositorio

```bash
git clone https://github.com/danjohn007/SoporteTecnico.git
cd SoporteTecnico
```

### 2. Configurar Apache

Asegúrate de que el módulo `mod_rewrite` esté habilitado:

```bash
# En Ubuntu/Debian
sudo a2enmod rewrite
sudo service apache2 restart

# En CentOS/RHEL
# Ya viene habilitado por defecto
```

### 3. Configurar la Base de Datos

1. Crea una base de datos MySQL:

```sql
CREATE DATABASE soporte_tecnico CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Importa el esquema de la base de datos:

```bash
mysql -u root -p soporte_tecnico < database.sql
```

### 4. Configurar Credenciales

Edita el archivo `config/config.php` y actualiza las credenciales de la base de datos:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'soporte_tecnico');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
```

### 5. Configurar Permisos

Asegúrate de que el directorio de uploads tenga permisos de escritura:

```bash
chmod -R 755 public/uploads/
chown -R www-data:www-data public/uploads/
```

### 6. Probar la Instalación

Accede a `http://tu-dominio/test_connection.php` para verificar que todo esté configurado correctamente.

## 🎯 Uso del Sistema

### Acceso Inicial

El sistema incluye datos de ejemplo con usuarios predefinidos:

#### Administrador
- **WhatsApp**: +524421234567

#### Agentes
- **WhatsApp**: +524421234568
- **WhatsApp**: +524421234569

#### Usuarios de Prueba
- **WhatsApp**: +524422345678 a +524422345682

### Crear un Nuevo Usuario

1. Ve a la página de registro
2. Ingresa tu nombre completo
3. Ingresa tu número de WhatsApp (formato: +52 442 123 4567)
4. El sistema creará tu cuenta automáticamente

### Crear un Ticket de Soporte

1. Inicia sesión
2. Ve a "Crear Ticket" desde el dashboard
3. Selecciona una categoría
4. Ingresa el asunto y descripción
5. Selecciona la prioridad
6. Opcionalmente adjunta archivos
7. Envía el ticket

### Usar el Chatbot

1. Accede a la sección "Chatbot"
2. Escribe tu pregunta
3. El sistema buscará en la base de FAQs
4. Si no encuentra respuesta, puedes crear un ticket directamente

## 📁 Estructura del Proyecto

```
SoporteTecnico/
├── app/
│   ├── controllers/         # Controladores MVC
│   │   ├── BaseController.php
│   │   ├── HomeController.php
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── TicketsController.php
│   │   ├── FaqController.php
│   │   ├── ChatbotController.php
│   │   ├── AdminController.php
│   │   └── SettingsController.php
│   ├── models/              # Modelos de datos
│   │   ├── User.php
│   │   ├── Session.php
│   │   ├── Ticket.php
│   │   ├── Category.php
│   │   ├── FAQ.php
│   │   ├── Chatbot.php
│   │   └── Setting.php
│   ├── views/               # Vistas
│   │   ├── layouts/
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── tickets/
│   │   ├── faq/
│   │   ├── chatbot/
│   │   ├── admin/
│   │   └── settings/
│   └── helpers.php          # Funciones auxiliares
├── config/
│   ├── config.php           # Configuración del sistema
│   └── database.php         # Conexión a BD
├── public/
│   ├── index.php            # Front controller
│   ├── .htaccess            # Reglas de reescritura
│   ├── css/                 # Estilos personalizados
│   ├── js/                  # Scripts JavaScript
│   └── uploads/             # Archivos subidos
├── .htaccess                # Configuración Apache raíz
├── database.sql             # Esquema de BD con datos de ejemplo
├── test_connection.php      # Test de configuración
└── README.md                # Este archivo
```

## ⚙️ Configuraciones del Sistema

### Módulo de Configuraciones

El sistema incluye un módulo completo de configuraciones accesible desde el panel de administración:

- **General**: Nombre del sitio, logo, zona horaria
- **Contacto**: Teléfonos, email, horarios de atención
- **Apariencia**: Colores personalizables del tema
- **Integraciones**: PayPal, API de QR, WhatsApp
- **Sistema**: Auto-cierre de tickets, configuraciones avanzadas

### URL Amigables

El sistema detecta automáticamente la URL base y funciona en cualquier directorio:

- `http://localhost/SoporteTecnico/`
- `http://midominio.com/`
- `http://midominio.com/soporte/`

### Personalización de Colores

Puedes cambiar los colores principales del sistema desde:
**Administración → Configuraciones → Apariencia**

## 🔒 Seguridad

- Autenticación basada en tokens de sesión
- Validación de entrada en todos los formularios
- Protección contra inyección SQL (PDO con prepared statements)
- Control de acceso basado en roles
- Auditoría completa de acciones
- Protección de archivos sensibles vía .htaccess
- Validación de tipos de archivo en uploads
- Headers de seguridad HTTP

## 📊 Base de Datos

El sistema incluye:
- **10 tablas principales**: users, sessions, tickets, categories, faqs, etc.
- **Datos de ejemplo de Querétaro**: Usuarios, categorías, tickets y FAQs
- **Índices optimizados** para consultas rápidas
- **Vistas SQL** para reportes

## 🔄 API REST (Preparada para Expansión)

El sistema está preparado para:
- Integración con aplicaciones móviles
- Webhooks de WhatsApp (Twilio/Meta)
- Microservicios
- API REST endpoints

## 📱 Notificaciones WhatsApp

Sistema preparado para integración con:
- Twilio WhatsApp API
- Meta WhatsApp Business API
- Otras plataformas de mensajería

## 🎨 Características de UI/UX

- Diseño responsive (móvil, tablet, desktop)
- Interfaz moderna y minimalista
- Iconos Font Awesome
- Tailwind CSS para estilos
- Animaciones suaves
- Feedback visual inmediato
- Mensajes de error y éxito contextuales

## 📈 Métricas y Reportes

El panel de administración incluye:
- Total de tickets por estado
- Tickets por prioridad
- Tickets por categoría
- Tiempo promedio de respuesta
- Desempeño por agente
- Gráficas y visualizaciones

## 🐛 Solución de Problemas

### Error 500 - Internal Server Error
- Verifica que mod_rewrite esté habilitado
- Revisa los permisos del directorio
- Comprueba los logs de Apache

### No se muestran los estilos
- Verifica la URL base en config.php
- Asegúrate de que Tailwind CSS se cargue desde CDN

### Error de conexión a la base de datos
- Verifica las credenciales en config.php
- Asegúrate de que MySQL esté corriendo
- Comprueba que la base de datos exista

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:
1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto es de código abierto y está disponible bajo la licencia MIT.

## 👨‍💻 Autor

- **GitHub**: [@danjohn007](https://github.com/danjohn007)

## 📞 Soporte

Para soporte o consultas:
- Abre un issue en GitHub
- Contacta al desarrollador

## 🎉 Agradecimientos

- Comunidad de PHP
- Tailwind CSS
- Font Awesome
- Todos los contribuidores

---

**Versión**: 1.0.0  
**Última actualización**: 2024  
**Estado**: Producción
