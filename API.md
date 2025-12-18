# API Documentation - Sistema de Soporte Técnico

## Introducción

El sistema expone endpoints API para integración con aplicaciones móviles, webhooks y servicios externos.

## Base URL

```
http://tu-dominio.com/
```

## Autenticación

La mayoría de endpoints requieren autenticación mediante token de sesión.

### Headers Requeridos

```http
X-Requested-With: XMLHttpRequest
Content-Type: application/x-www-form-urlencoded
```

Para endpoints que requieren autenticación, incluir el token en cookies de sesión.

---

## Endpoints Disponibles

### 1. Autenticación

#### Registro de Usuario
```http
POST /auth/doRegister
```

**Parámetros:**
- `nombre_completo` (string, requerido): Nombre completo del usuario
- `whatsapp` (string, requerido): Número de WhatsApp en formato +52 442 123 4567

**Respuesta Éxito:**
- Redirección a `/dashboard`
- Session creada automáticamente

**Respuesta Error:**
- Redirección a `/auth/register` con errores en sesión

---

#### Iniciar Sesión
```http
POST /auth/doLogin
```

**Parámetros:**
- `whatsapp` (string, requerido): Número de WhatsApp registrado

**Respuesta Éxito:**
- Redirección a `/dashboard`
- Token de sesión generado

**Respuesta Error:**
- Redirección a `/auth/login` con errores

---

#### Cerrar Sesión
```http
GET /auth/logout
```

**Respuesta:**
- Redirección a `/home`
- Sesión destruida

---

### 2. Tickets

#### Crear Ticket
```http
POST /tickets/store
```

**Parámetros:**
- `category_id` (int, requerido): ID de la categoría
- `asunto` (string, requerido): Asunto del ticket
- `descripcion` (string, requerido): Descripción detallada
- `prioridad` (string, opcional): baja, media, alta, critica (default: media)
- `attachment` (file, opcional): Archivo adjunto

**Respuesta Éxito:**
- Redirección a `/tickets/view/{id}`
- Ticket creado con ID único

---

#### Responder a Ticket
```http
POST /tickets/reply/{ticket_id}
```

**Parámetros:**
- `mensaje` (string, requerido): Mensaje de respuesta
- `attachment` (file, opcional): Archivo adjunto

**Respuesta:**
- Redirección a `/tickets/view/{ticket_id}`

---

#### Cambiar Estado de Ticket (Solo Agentes)
```http
POST /tickets/changeStatus/{ticket_id}
```

**Parámetros:**
- `status` (string, requerido): abierto, en_proceso, en_espera_cliente, resuelto, cerrado

**Respuesta:**
- Redirección a `/tickets/view/{ticket_id}`

---

#### Asignar Ticket (Solo Agentes)
```http
POST /tickets/assign/{ticket_id}
```

**Parámetros:**
- `agent_id` (int, opcional): ID del agente (default: usuario actual)

**Respuesta:**
- Redirección a `/tickets/view/{ticket_id}`

---

### 3. FAQ

#### Buscar FAQs (AJAX)
```http
GET /faq/search?q={query}
```

**Parámetros:**
- `q` (string, requerido): Término de búsqueda

**Respuesta JSON:**
```json
{
  "results": [
    {
      "id": 1,
      "pregunta": "¿Cómo reinicio mi equipo?",
      "respuesta": "Para reiniciar...",
      "categoria_nombre": "Técnico",
      "views": 100,
      "helpful_count": 25
    }
  ]
}
```

---

#### Marcar FAQ como Útil (AJAX)
```http
POST /faq/helpful/{faq_id}
```

**Parámetros:**
- `helpful` (string, requerido): yes o no

**Respuesta JSON:**
```json
{
  "success": true,
  "message": "Gracias por tu feedback"
}
```

---

### 4. Chatbot

#### Enviar Mensaje al Chatbot (AJAX)
```http
POST /chatbot/message
```

**Parámetros:**
- `message` (string, requerido): Mensaje del usuario
- `session_id` (string, opcional): ID de sesión del chat

**Respuesta JSON (Con coincidencia):**
```json
{
  "response": {
    "type": "faq",
    "id": 1,
    "pregunta": "¿Cómo reinicio mi equipo?",
    "respuesta": "Para reiniciar tu equipo...",
    "categoria": "Técnico"
  },
  "faqs": [...],
  "session_id": "chat_123456789",
  "has_match": true
}
```

**Respuesta JSON (Sin coincidencia):**
```json
{
  "response": {
    "type": "no_match",
    "message": "Lo siento, no encontré información...",
    "suggestions": [
      "Crear un ticket de soporte",
      "Ver todas las preguntas frecuentes",
      "Intentar con otra pregunta"
    ]
  },
  "faqs": [],
  "session_id": "chat_123456789",
  "has_match": false
}
```

---

#### Crear Ticket desde Chat
```http
POST /chatbot/createTicket
```

**Parámetros:**
- `session_id` (string, requerido): ID de sesión del chat
- `category_id` (int, requerido): ID de categoría
- `asunto` (string, requerido): Asunto del ticket
- `descripcion` (string, requerido): Descripción
- `prioridad` (string, opcional): Prioridad del ticket

**Respuesta JSON:**
```json
{
  "success": true,
  "ticket_id": "TKT-ABC123",
  "message": "Ticket creado exitosamente"
}
```

---

### 5. Administración

#### Guardar Categoría (Solo Admin)
```http
POST /admin/saveCategory
```

**Parámetros:**
- `id` (int, opcional): ID para actualizar, vacío para crear
- `nombre` (string, requerido): Nombre de la categoría
- `descripcion` (string, opcional): Descripción

---

#### Guardar FAQ (Solo Admin)
```http
POST /admin/saveFaq
```

**Parámetros:**
- `id` (int, opcional): ID para actualizar
- `category_id` (int, opcional): ID de categoría
- `pregunta` (string, requerido): Pregunta
- `respuesta` (string, requerido): Respuesta
- `keywords` (string, opcional): Palabras clave separadas por comas

---

#### Actualizar Rol de Usuario (Solo Admin)
```http
POST /admin/updateUserRole
```

**Parámetros:**
- `user_id` (int, requerido): ID del usuario
- `role` (string, requerido): user, agent o admin

---

### 6. Configuraciones

#### Guardar Configuraciones (Solo Admin)
```http
POST /settings/save
```

**Parámetros:**
- `settings[{key}]` (mixed): Array de configuraciones
- `site_logo` (file, opcional): Logo del sitio

**Ejemplo:**
```
settings[site_name]=Mi Sistema
settings[primary_color]=#3B82F6
settings[auto_close_days]=7
```

---

## Códigos de Estado HTTP

- `200 OK`: Solicitud exitosa
- `400 Bad Request`: Parámetros inválidos
- `401 Unauthorized`: No autenticado
- `403 Forbidden`: Sin permisos
- `404 Not Found`: Recurso no encontrado
- `500 Internal Server Error`: Error del servidor

---

## Ejemplos de Uso

### JavaScript/Fetch - Buscar FAQ

```javascript
const searchFAQ = async (query) => {
  const response = await fetch(`/faq/search?q=${encodeURIComponent(query)}`, {
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    }
  });
  
  const data = await response.json();
  return data.results;
};
```

### JavaScript/Fetch - Enviar Mensaje al Chatbot

```javascript
const sendChatMessage = async (message, sessionId) => {
  const response = await fetch('/chatbot/message', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: `message=${encodeURIComponent(message)}&session_id=${sessionId}`
  });
  
  return await response.json();
};
```

### PHP/cURL - Crear Ticket

```php
$data = [
    'category_id' => 1,
    'asunto' => 'Problema con mi cuenta',
    'descripcion' => 'No puedo acceder...',
    'prioridad' => 'alta'
];

$ch = curl_init('http://tu-dominio.com/tickets/store');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
curl_close($ch);
```

---

## Webhooks (Preparado para Integración)

### WhatsApp Notifications

El sistema está preparado para integrar webhooks de WhatsApp:

**Configuración requerida:**
1. Configurar `WHATSAPP_API_ENABLED = true` en config.php
2. Configurar `WHATSAPP_API_KEY` y `WHATSAPP_API_URL`
3. Implementar lógica de envío en helpers.php

**Eventos que disparan notificaciones:**
- Ticket creado
- Ticket asignado
- Cambio de estado
- Nueva respuesta del agente
- Ticket resuelto/cerrado

---

## Rate Limiting

No implementado actualmente. Considerar implementar en producción:
- Límite sugerido: 100 requests por minuto por IP
- Límite para APIs: 1000 requests por hora por token

---

## Versionamiento

Versión actual: **v1.0.0**

El sistema no usa versionamiento de API en URLs actualmente. Considerar implementar para futuras versiones:
- `/api/v1/tickets`
- `/api/v2/tickets`

---

## Soporte y Contacto

Para soporte técnico o consultas sobre la API:
- GitHub Issues: https://github.com/danjohn007/SoporteTecnico/issues
- Email: soporte@example.com

---

**Nota**: Esta documentación corresponde a la versión 1.0.0 del sistema. Para implementación en producción, considerar agregar:
- Autenticación JWT
- Rate limiting
- Versionamiento de API
- Documentación Swagger/OpenAPI
- Monitoreo y logs
