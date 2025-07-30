# README - Employee Management API

Este proyecto es una API construida en Symfony que permite gestionar empleados. Incluye autenticación JWT, un microservicio en Python para enviar notificaciones por correo.

---

## Instalación y Ejecución

### Prerrequisitos

* Docker
* Docker Compose
* Make (opcional)

### Clonar el Repositorio

```bash
git clone <REPO_URL>
cd employee-management-app
```

### Configurar Variables de Entorno

Edita o crea el archivo `.env`:

```env
APP_ENV=dev
DATABASE_URL=mysql://root:rootpassword@db:3306/employee_management_db?serverVersion=8.0.32&charset=utf8mb4
NOTIFICATION_SERVICE_URL=http://notification-service:8000/notify
JWT_SECRET_KEY=employee
```

### Levantar Contenedores

```bash
docker-compose up --build -d
```

Esto Inicia:

API Symfony en http://localhost:8000
Microservicio de notificaciones en http://localhost:8001
MySQL en localhost:3306

### Inicializar Base de Datos

```bash
docker-compose exec -it php bash
php bin/console doctrine:migrations:migrate
```

### Generar Claves JWT

```bash
mkdir -p config/jwt
openssl genrsa -out config/jwt/private.pem -aes256 4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

### Ejecutar Pruebas

```bash
make test
```

---

## Frontend React

### Struture

La aplicación frontend está en la carpeta frontend/
Utiliza React + Vite + Bootstrap

```bash
docker-compose exec -it frontend npm install
npm run dev
```

Esto Inicia:
El frontend de React: http://localhost:5174

## Endpoints

### Autenticación

* `POST /api/register`
* `POST /api/login_check`

### Empleados

* `GET /api/employees`
* `POST /api/employees`
* `GET /api/employees/{id}`
* `PUT /api/employees/{id}`
* `DELETE /api/employees/{id}`
* `GET /api/employees/search/{name}`

### Notificaciones

* Al crear un empleado, se envía un email automáticamente desde el microservicio.

> Rutas abiertas: `/api/register`, `/api/login_check`
> Resto requiere autenticación con JWT (Bearer Token)

---

## 🔄 Arquitectura del Sistema

```
+------------------+           +--------------------------+
|  Cliente (React) |  <----->  |   API Symfony (PHP 8.2)  |
+------------------+           +-----------+--------------+
                                             |
                                             |
                             +---------------v-----------------+
                             |      MySQL (employee DB)        |
                             +----------------------------------+
                                             |
                                             |
                        +--------------------v-------------------+
                        |   Microservicio de Notificaciones      |
                        |     Flask + smtplib / Mailtrap         |
                        +----------------------------------------+
```

---
