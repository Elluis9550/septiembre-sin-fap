# Septiembre Sin Fap 🚀

Una app web para mantener una racha de 30 días sin contenido adulto. Sistema de reportes diarios, rankings de participantes y "Salón de los caídos".

## Características

- ✅ Sistema de autenticación por sesión
- 📊 Dashboard con ranking de participantes activos
- 📅 Reportes diarios (sobreviví/fallé)
- 👑 Rangos dinámicos según días alcanzados (Novato → Leyenda)
- 💀 Salón de los caídos: histórico de quiénes caen y cuándo
- 🌙 Interfaz oscura minimalista con identidad visual fuerte
- ⚡ API JSON para todas las operaciones
- 🔒 Transacciones atómicas en caídas
- 📍 Timezone configurable

## Stack

- **Backend**: PHP 8.2+ puro (sin frameworks)
- **Base de datos**: PostgreSQL (Neon)
- **Frontend**: HTML5 + CSS3 + JavaScript (vanilla)
- **Hosting**: Render (PHP)

## Instalación Local

### Requisitos

- PHP 8.2+
- PostgreSQL 14+ (o Neon)
- Extensiones: `pdo_pgsql`, `pgsql`

### Pasos

1. Clonar el repositorio:
   ```bash
   git clone https://github.com/TU_USUARIO/septiembre-sin-fap.git
   cd septiembre-sin-fap
   ```

2. Crear `.env` desde el ejemplo:
   ```bash
   cp .env.example .env
   ```

3. Editar `.env` con tus datos de Neon:
   ```env
   DB_HOST=ep-xxxx-xxxx-pooler.us-east-2.aws.neon.tech
   DB_PORT=5432
   DB_NAME=tu_db
   DB_USER=tu_usuario
   DB_PASSWORD=tu_password
   DB_SSLMODE=require
   DB_OPTIONS=endpoint=ep-xxxx-xxxx
   APP_TIMEZONE=America/Bogota
   ```

4. Crear las tablas en PostgreSQL:
   ```bash
   psql "postgresql://usuario:password@host:5432/dbname?sslmode=require" -f database/schema.sql
   ```

5. Ejecutar servidor PHP:
   ```bash
   cd public
   php -S localhost:8000
   ```

6. Abrir http://localhost:8000 en el navegador

## Despliegue en Render

### 1. Crear repositorio en GitHub

```bash
git remote add origin https://github.com/TU_USUARIO/septiembre-sin-fap.git
git branch -M main
git push -u origin main
```

### 2. Crear Web Service en Render

1. Ir a https://render.com
2. Crear nuevo "Web Service"
3. Conectar repositorio de GitHub
4. Configurar:

   | Campo | Valor |
   |-------|-------|
   | Runtime | PHP |
   | Build Command | `composer install --no-dev` (o vacío si no usa composer) |
   | Start Command | (Render lo detecta automáticamente para PHP) |
   | Root Directory | (raíz del repo) |

### 3. Configurar variables de entorno

En Render Dashboard → tu Web Service → Environment:

```env
DB_HOST=ep-xxxx-xxxx-pooler.c-4.us-east-2.aws.neon.tech
DB_PORT=5432
DB_NAME=neondb
DB_USER=neondb_owner
DB_PASSWORD=npg_ikz7aEAld1uj
DB_SSLMODE=require
DB_OPTIONS=endpoint=ep-xxxx-xxxx
APP_TIMEZONE=America/Bogota
```

### 4. Configurar Document Root

En Render, si es necesario, crea un archivo `render.yaml` en la raíz:

```yaml
services:
  - type: web
    name: septiembre-sin-fap
    env: php
    startCommand: "php -S 0.0.0.0:${PORT:-3000} -t public"
```

## Crear usuarios de prueba

Ejecuta el script de generación:

```bash
php scripts/crear_usuarios_reto.php
```

Las credenciales se guardan en `scripts/credenciales_usuarios.txt`

## API Endpoints

### Públicos (requieren autenticación)

- `GET /api/usuarios.php?orden=dias` — Lista de participantes activos
- `GET /api/caidos.php` — Historial de caídas
- `GET /api/usuario.php` — Datos del usuario actual
- `GET /api/reportes.php?desde=YYYY-MM-DD&hasta=YYYY-MM-DD` — Reportes del usuario
- `GET /api/estado.php` — Estado del reporte diario
- `POST /api/reportar.php` — Registrar reporte (sobreviví/fallé)

### Públicos sin sesión

- `POST /api/login.php` — Autenticarse
- `GET /api/logout.php` — Cerrar sesión

## Estructura

```
septiembre-sin-fap/
├── backend/                  # Repositorios (acceso a BD)
│   ├── caidas/
│   ├── reportes/
│   └── usuarios/
├── config/                   # Configuración centralizada
│   ├── app.php              # Constantes del reto (fechas, timezones)
│   ├── database.php         # Conexión a PostgreSQL/Neon
│   └── env.php              # Cargador de .env
├── database/
│   └── schema.sql           # DDL de tablas
├── functions/               # Helpers
│   ├── auth.php             # Sesiones y autenticación
│   ├── helpers.php          # JSON, sanitización
│   └── rangos.php           # Tabla de rangos
├── public/                  # Document root
│   ├── api/                 # Endpoints JSON
│   ├── css/style.css        # Estilos únicos (Oswald + Manrope)
│   ├── js/                  # Scripts del lado cliente
│   ├── caidos.php           # Página: Salón de los caídos
│   ├── dashboard.php        # Página: Dashboard principal
│   ├── login.php            # Página: Autenticación
│   ├── perfil.php           # Página: Perfil de usuario
│   ├── index.php            # Redirección al login/dashboard
│   └── logo.png             # Marca del proyecto
├── scripts/                 # Herramientas
│   ├── cerrar_dia.php       # Cron: cierre automático de día
│   └── crear_usuarios_reto.php # Generador de usuarios de prueba
├── .env.example             # Template de variables de entorno
├── .gitignore               # Archivos a ignorar
└── README.md                # Este archivo
```

## Notas de desarrollo

- **Transacciones**: Las caídas usan transacciones `BEGIN/COMMIT/ROLLBACK` para garantizar consistencia
- **Seguridad de contraseñas**: `password_hash()` con `PASSWORD_DEFAULT` (bcrypt)
- **Inyección SQL**: Todas las queries usan prepared statements
- **XSS**: Todas las salidas usan `htmlspecialchars()`
- **Sesiones**: Cookie `httponly`, regeneración de ID en login, `SameSite=Lax`
- **Cron**: El script `cerrar_dia.php` debe ejecutarse diariamente a las 05:00 UTC (= medianoche Bogotá)

## Configuración del cron en Render

Render no tiene integración nativa de cron, así que tienes dos opciones:

1. **Usar un servicio externo** (EasyCron, cron-job.org)
2. **Usar GitHub Actions** para hacer un request HTTP diario

Ejemplo con curl (para configurar en un servicio de cron):

```bash
curl -X GET "https://tu-app.onrender.com/scripts/cerrar_dia.php"
```

## Troubleshooting

### Error: "Endpoint ID is not specified"

**Causa**: Neon requiere SNI en libpq viejo.

**Solución**: Asegúrate de que `DB_OPTIONS` contiene `endpoint=ep-xxxx-xxxx` en `.env`.

### Error 404 en /

**Causa**: Render no apunta al Document Root correcto.

**Solución**: En Render, verifica que el Start Command sea `php -S 0.0.0.0:${PORT:-3000} -t public` o similar.

### No se ve el logo

**Causa**: Rutas incorrectas o logo no en `public/`.

**Solución**: Verifica que `public/logo.png` exista y las referencias HTML apunten a `logo.png` (relativo).

## Licencia

MIT

## Autor

Septiembre Sin Fap Team
