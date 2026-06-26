# Biblioteca — Prueba Técnica Backend/Fullstack

Sistema de gestión de biblioteca construido con **Laravel 8 + PostgreSQL + Sanctum**, siguiendo arquitectura por capas (Repository + Service + Domain).

---

## 1. Requisitos

- PHP 8.0+ con extensiones: `pdo_pgsql`, `pgsql`, `mbstring`, `openssl`, `fileinfo`, `tokenizer`, `curl`, `zip`
- Composer 2.x
- PostgreSQL 13+
- (Opcional) Postman para probar la API

## 2. Instalación

```powershell
# 1. Instalar dependencias
composer install

# 2. Crear archivo de entorno y generar APP_KEY
copy .env.example .env
php artisan key:generate

# 3. Editar .env con credenciales de PostgreSQL
#    DB_CONNECTION=pgsql
#    DB_HOST=localhost
#    DB_PORT=5432
#    DB_DATABASE=biblioteca
#    DB_USERNAME=postgres
#    DB_PASSWORD=tu_password

# 4. Crear la base de datos (una sola vez)
psql -U postgres -c "CREATE DATABASE biblioteca WITH ENCODING='UTF8';"

# 5. Correr migraciones + seeders (10 autores, 20 libros, 15 usuarios, 10 préstamos + admin)
php artisan migrate:fresh --seed

# 6. Levantar el servidor
php artisan serve
# → http://127.0.0.1:8000
```

### Credenciales por defecto

| Email                       | Password      | Rol                                          |
|-----------------------------|---------------|----------------------------------------------|
| `admin@biblioteca.test`     | `password123` | Bibliotecario (autenticación API vía Sanctum) |

---

## 3. Mapa de entregables del spec

| Parte del spec                              | Ubicación en el repo                                                                                                                                                                                                                              |
|---------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| **1.1 Script DDL SQL**                      | [`database/sql/schema.sql`](database/sql/schema.sql)                                                                                                                                                                                              |
| **2.1 Migraciones Laravel**                 | [`database/migrations/`](database/migrations/) — `2026_06_26_201134_create_autores_table.php`, `..._create_usuarios_table.php`, `..._create_libros_table.php`, `..._create_autor_libro_table.php`, `..._create_prestamos_table.php`                |
| **2.2 Seeders (10/20/15/10 + admin)**       | [`database/seeders/`](database/seeders/) — `DatabaseSeeder`, `UserSeeder`, `AutorSeeder`, `LibroSeeder`, `UsuarioSeeder`, `PrestamoSeeder`; factories en [`database/factories/`](database/factories/)                                              |
| **2.3 Configuración PostgreSQL**            | [`config/database.php`](config/database.php) (driver `pgsql`) + variables en `.env`                                                                                                                                                               |
| **3.1 Modelos Eloquent**                    | [`app/Models/`](app/Models/) — `Autor.php`, `Libro.php`, `Usuario.php`, `Prestamo.php`                                                                                                                                                            |
| **3.2 Scopes en Libro** (`disponibles`, `porAnio`, `porAutor`) | [`app/Models/Libro.php`](app/Models/Libro.php)                                                                                                                                                                                              |
| **3.3 Pivot personalizado con `orden_autor`** | [`app/Models/Pivots/AutorLibro.php`](app/Models/Pivots/AutorLibro.php)                                                                                                                                                                          |
| **4.1 API REST (libros + préstamos + autores)** | [`app/Http/Controllers/Api/`](app/Http/Controllers/Api/) + [`routes/api.php`](routes/api.php)                                                                                                                                                  |
| **4.2 Form Requests** (validación)          | [`app/Http/Requests/`](app/Http/Requests/) — `StoreLibroRequest`, `UpdateLibroRequest`, `StorePrestamoRequest`, `StoreAutorRequest`, `UpdateAutorRequest`                                                                                          |
| **4.3 Respuestas JSON consistentes**        | [`app/Http/Resources/`](app/Http/Resources/) + [`app/Exceptions/Handler.php`](app/Exceptions/Handler.php) (códigos 201/204/401/404/409/422)                                                                                                       |
| **4.4 Sanctum middleware**                  | [`routes/api.php`](routes/api.php) — grupo `auth:sanctum` + [`app/Http/Controllers/Api/AuthController.php`](app/Http/Controllers/Api/AuthController.php)                                                                                          |
| **5.1 Reglas de negocio** (stock, 3 préstamos, autor con libros, vencidos) | [`app/Services/PrestamoService.php`](app/Services/PrestamoService.php), [`app/Services/AutorService.php`](app/Services/AutorService.php) + excepciones en [`app/Domain/Exceptions/`](app/Domain/Exceptions/) |
| **5.2 Comando Artisan de reporte**          | [`app/Console/Commands/ReporteBibliotecaCommand.php`](app/Console/Commands/ReporteBibliotecaCommand.php) → `php artisan reporte:biblioteca`                                                                                                       |
| **5 Scheduler** (préstamos vencidos)        | [`app/Console/Commands/MarcarPrestamosVencidosCommand.php`](app/Console/Commands/MarcarPrestamosVencidosCommand.php) + registro en [`app/Console/Kernel.php`](app/Console/Kernel.php) (`->dailyAt('02:00')`)                                       |
| **7 Postman**                               | [`postman/Biblioteca.postman_collection.json`](postman/Biblioteca.postman_collection.json) + [`postman/Biblioteca.postman_environment.json`](postman/Biblioteca.postman_environment.json)                                                          |
| **Arquitectura: Repositorios + Servicios + Dominio** | [`app/Repositories/`](app/Repositories/) (contratos + implementaciones Eloquent), [`app/Services/`](app/Services/), [`app/Domain/`](app/Domain/), [`app/Providers/RepositoryServiceProvider.php`](app/Providers/RepositoryServiceProvider.php)  |

---

## 4. Endpoints API

| Método | URL                                  | Descripción                                       |
|--------|--------------------------------------|---------------------------------------------------|
| POST   | `/api/login`                         | Login → devuelve `token` Sanctum                   |
| GET    | `/api/me`                            | Perfil autenticado                                |
| POST   | `/api/logout`                        | Cierra sesión (invalida token actual)             |
| GET    | `/api/libros`                        | Lista paginada con filtros `titulo`, `autor`, `anio`, `disponibles` |
| POST   | `/api/libros`                        | Crear libro + asociar autores (con `orden_autor`) |
| GET    | `/api/libros/{id}`                   | Detalle con autores embebidos                     |
| PUT    | `/api/libros/{id}`                   | Actualizar libro                                  |
| DELETE | `/api/libros/{id}`                   | **Soft delete**                                   |
| GET    | `/api/autores`                       | Listado paginado                                  |
| POST   | `/api/autores`                       | Crear autor                                       |
| GET    | `/api/autores/{id}`                  | Detalle                                           |
| PUT    | `/api/autores/{id}`                  | Actualizar                                        |
| DELETE | `/api/autores/{id}`                  | Eliminar (409 si tiene libros)                    |
| GET    | `/api/prestamos`                     | Lista con `usuario` y `libro` embebidos           |
| POST   | `/api/prestamos`                     | Crear préstamo (valida stock + máx. 3 activos)    |
| PUT    | `/api/prestamos/{id}/devolver`       | Marcar como devuelto (incrementa stock)           |

Todas las rutas (excepto `/api/login`) requieren header `Authorization: Bearer <token>`.

---

## 5. Comandos útiles

```powershell
# Reset completo de la BD con datos de prueba
php artisan migrate:fresh --seed

# Reporte gerencial en consola (tabla o JSON)
php artisan reporte:biblioteca
php artisan reporte:biblioteca --top=5 --format=json

# Marcar préstamos vencidos manualmente (regla del spec: 15 días de gracia)
php artisan prestamos:marcar-vencidos
php artisan prestamos:marcar-vencidos --dias=30

# Ver tareas programadas
php artisan schedule:list

# Correr el scheduler localmente
php artisan schedule:work

# Ver todas las rutas API
php artisan route:list --path=api
```

---

## 6. Estructura del proyecto (arquitectura por capas)

```
app/
├── Domain/                              ← REGLAS DE NEGOCIO PURAS (sin Laravel)
│   ├── Enums/                           ← EstadoUsuario, EstadoPrestamo
│   └── Exceptions/                      ← StockInsuficiente, LimitePrestamosExcedido, etc.
├── Models/                              ← ELOQUENT (capa de persistencia)
│   ├── Autor.php, Libro.php, Usuario.php, Prestamo.php
│   └── Pivots/AutorLibro.php            ← pivot personalizado con orden_autor
├── Repositories/
│   ├── Contracts/                       ← interfaces
│   └── Eloquent/                        ← implementaciones
├── Services/                            ← CASOS DE USO (PrestamoService, etc.)
├── Http/
│   ├── Controllers/Api/                 ← controllers DELGADOS, solo orquestan
│   ├── Requests/                        ← Form Requests (validación)
│   └── Resources/                       ← Transformers JSON
├── Console/Commands/                    ← reporte:biblioteca, prestamos:marcar-vencidos
└── Providers/RepositoryServiceProvider.php  ← binding interfaces → Eloquent

database/
├── migrations/                          ← migraciones Laravel
├── seeders/                             ← seeders por entidad
├── factories/                           ← factories con faker
└── sql/schema.sql                       ← DDL puro PostgreSQL (entregable parte 1.1)

postman/
├── Biblioteca.postman_collection.json   ← 20+ peticiones con tests automatizados
└── Biblioteca.postman_environment.json  ← variables: base_url, token, libro_id, etc.
```

---

## 7. Reglas de negocio implementadas

| # | Regla                                                                    | Dónde se enforza                                                                                          |
|---|--------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------|
| 1 | No prestar libro si `stock_disponible == 0`                              | `PrestamoService::crearPrestamo` con `lockForUpdate` + decremento atómico                                  |
| 2 | Usuario no puede tener más de **3 préstamos activos** simultáneos        | `PrestamoService::crearPrestamo` con `contarActivosPorUsuario`                                            |
| 3 | No se puede eliminar un autor que tiene libros asociados                 | `AutorService::eliminar` lanza `AutorConLibrosException` → HTTP 409                                       |
| 4 | Préstamos con más de 15 días de atraso se marcan como `vencido`          | `MarcarPrestamosVencidosCommand` + scheduler diario en `Console/Kernel.php`                               |
| 5 | Préstamo devuelto incrementa stock + cambia estado a `devuelto`          | `PrestamoService::devolverPrestamo` (en transacción)                                                       |
| 6 | Usuario inactivo no puede tomar préstamos                                | `PrestamoService::crearPrestamo` lanza `UsuarioInactivoException`                                         |

---

## 8. Probar la API con Postman

1. Importar `postman/Biblioteca.postman_collection.json`
2. Importar `postman/Biblioteca.postman_environment.json`
3. Seleccionar environment **"Biblioteca (Local)"**
4. Levantar el servidor: `php artisan serve` (asegúrate que use el puerto **8000**)
5. Ejecutar **`Auth → Login (success)`** — el token se guarda automáticamente
6. Probar el resto de peticiones

Todas las peticiones tienen tests automatizados (`pm.test`) que verifican status code y estructura JSON.
