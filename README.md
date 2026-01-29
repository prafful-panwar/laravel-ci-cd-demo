# Task App (Laravel + Vue)

A full-stack task management application built with Laravel 12 and Vue 3. Includes REST API, real-time task management, Docker support, and comprehensive testing.

## Setup

1.  **Clone & Install**

    ```bash
    git clone https://github.com/prafful-panwar/task-app-ci-cd-demo.git
    cd task-app-ci-cd-demo
    composer install
    cp .env.example .env
    php artisan key:generate
    npm install && npm run build
    ```

2.  **Configuration**
    Update your `.env` file to match your environment:

    ```ini
    APP_NAME="App Name"
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=db_name
    DB_USERNAME=root
    DB_PASSWORD=
    ```

3.  **Database & Seeding**
    Run migrations and (optionally) seed the database:

    ```bash
    php artisan migrate
    php artisan db:seed
    ```

4.  **Start Server (Local)**

    **Important:** Port `8000` is reserved for Docker. To avoid conflicts (or if you want to run both), always run the local server on a different port (e.g., 8001).

    ```bash
    php artisan serve --port=8001
    ```

    The API will be available at: `http://127.0.0.1:8001/api/tasks` (and other endpoints)

    _(Or if using Laravel Herd: `http://task-app-ci-cd.test/api/tasks`)_

## Docker Setup (Recommended)

You can run the entire application (Frontend + Backend + Database) using Docker.

1.  **Prerequisites**
    - Ensure [Docker Desktop](https://www.docker.com/products/docker-desktop/) is installed and running.

2.  **Start Application**

    ```bash
    docker-compose up -d --build
    ```

3.  **Access Application**
    - **Frontend**: [http://localhost:8000](http://localhost:8000)
    - **API**: [http://localhost:8000/api/tasks](http://localhost:8000/api/tasks)

4.  **Verify Frontend (Optional)**

    Run the browser tests to verify the frontend loads correctly:

    ```bash
    docker-compose exec app php artisan test tests/Browser/LandingPageTest.php
    ```

    This verifies the page loads without JavaScript errors and displays the app correctly.

5.  **Stop Application**
    ```bash
    docker-compose down
    ```

## Quick Start with Docker Hub

If you want to run this application without cloning the repository, you can use the pre-built images from Docker Hub.

### 1. Create Configuration Files

Create a new directory (e.g., `task-app`) and creates a `.env` file inside it:

```ini
APP_HOST=localhost
APP_PORT=8100
```

### 2. Create Docker Compose File

Create a `docker-compose.yml` file in the same directory:

```yaml
version: '3.8'

services:
    app:
        image: praffulpanwar2016/task-app-ci-cd-demo:latest
        platform: linux/amd64
        restart: unless-stopped
        working_dir: /var/www
        depends_on:
            - db
        environment:
            APP_URL: http://${APP_HOST}:${APP_PORT}
            CORS_ALLOWED_ORIGINS: http://${APP_HOST}:${APP_PORT}
            SANCTUM_STATEFUL_DOMAINS: ${APP_HOST}:${APP_PORT}
            APP_KEY: base64:2fl+Ktvkfl+Frkxvry1vF+I5/1G5Q/5K5+555555555=
            APP_DEBUG: true
            DB_CONNECTION: mysql
            DB_HOST: db
            DB_PORT: 3306
            DB_DATABASE: task-app-ci-cd-demo
            DB_USERNAME: laravel
            DB_PASSWORD: password
        networks:
            - task-app-network

    web:
        image: praffulpanwar2016/task-app-nginx:latest
        platform: linux/amd64
        restart: unless-stopped
        ports:
            - '${APP_PORT}:80'
        depends_on:
            - app
        networks:
            - task-app-network

    db:
        image: mysql:8.0
        restart: unless-stopped
        environment:
            MYSQL_DATABASE: task-app-ci-cd-demo
            MYSQL_USER: laravel
            MYSQL_PASSWORD: password
            MYSQL_ROOT_PASSWORD: root
        volumes:
            - dbdata:/var/lib/mysql
        networks:
            - task-app-network

networks:
    task-app-network:
        driver: bridge

volumes:
    dbdata:
```

### 3. Run the Application

```bash
docker-compose up -d
```

The application will be available at `http://localhost:8100` (or whatever host/port you configured in `.env`).

## Development & Quality Assurance

### Running Tests

Run the complete test suite:

```bash
php artisan test
```

### Static Analysis (Larastan)

Run PHPStan for static analysis:

```bash
./vendor/bin/phpstan analyse --memory-limit=2G
```

### Code Formatting (Pint)

Fix code style issues automatically:

```bash
./vendor/bin/pint --parallel
```

### Profanity Check

Check for profanity in the codebase:

```bash
./vendor/bin/pest --profanity
```

### Type Coverage

Check type coverage (enforces 100%):

```bash
php -d memory_limit=2G ./vendor/bin/pest --type-coverage
```

### IDE Helper

Generate helper files for IDE autocompletion:

```bash
php artisan ide-helper:generate
php artisan ide-helper:meta
php artisan ide-helper:models --nowrite
```

### Frontend Linting

Lint and fix frontend code (Vue/JS):

```bash
npm run lint
```

### Git Hooks (Husky)

Commands run automatically before commit:

- `Pint` (Formats PHP code)
- `Pest` (Runs tests)

## API Documentation

### Endpoints

| Method   | Endpoint      | Description                                      |
| :------- | :------------ | :----------------------------------------------- |
| `GET`    | `/tasks`      | List all tasks (supports pagination & filtering) |
| `POST`   | `/tasks`      | Create a new task                                |
| `GET`    | `/tasks/{id}` | Show a specific task                             |
| `PUT`    | `/tasks/{id}` | Update a task (full or partial)                  |
| `DELETE` | `/tasks/{id}` | Delete a task                                    |

### Examples

#### 1. List Tasks

**GET** `/tasks?status=pending&per_page=5`

**Status:** `200 OK`

**Response:**

```json
{
    "data": [
        {
            "id": 1,
            "title": "Task 1",
            "description": "Description 1",
            "status": "pending",
            "status_label": "Pending",
            "due_date": "2024-01-25",
            "due_date_human": "January 25, 2024",
            "created_at": "2024-01-10T08:00:00.000000Z",
            "created_at_human": "1 week ago",
            "updated_at": "2024-01-10T08:00:00.000000Z"
        }
    ],
    "links": {
        "first": "http://localhost/api/tasks?page=1",
        "last": "http://localhost/api/tasks?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http://localhost/api/tasks",
        "per_page": 5,
        "to": 1,
        "total": 1
    }
}
```

#### 2. Get Task

**GET** `/tasks/1`

**Status:** `200 OK`

**Response:**

```json
{
    "data": {
        "id": 1,
        "title": "Task 1",
        "description": "Description 1",
        "status": "pending",
        "status_label": "Pending",
        "due_date": "2024-01-25",
        "due_date_human": "January 25, 2024",
        "created_at": "2024-01-10T08:00:00.000000Z",
        "created_at_human": "1 week ago",
        "updated_at": "2024-01-10T08:00:00.000000Z"
    }
}
```

#### 3. Create Task

**POST** `/tasks`

**Status:** `201 Created`

**Request:**

```json
{
    "title": "Fix Mobile API",
    "description": "Add human readable dates",
    "status": "in_progress",
    "due_date": "2024-01-20"
}
```

**Response:**

```json
{
    "data": {
        "id": 21,
        "title": "Fix Mobile API",
        "description": "Add human readable dates",
        "status": "in_progress",
        "status_label": "In Progress",
        "due_date": "2024-01-20",
        "due_date_human": "January 20, 2024",
        "created_at": "2024-01-21T10:00:00.000000Z",
        "created_at_human": "1 second ago",
        "updated_at": "2024-01-21T10:00:00.000000Z"
    }
}
```

#### 4. Update Task

**PUT** `/tasks/1`

**Status:** `200 OK`

**Request:**

```json
{
    "status": "completed"
}
```

**Response:**

```json
{
    "data": {
        "id": 1,
        "title": "Existing Task Title",
        "description": "Existing description",
        "status": "completed",
        "status_label": "Completed",
        "due_date": "2024-01-25",
        "due_date_human": "January 25, 2024",
        "created_at": "2024-01-10T08:00:00.000000Z",
        "created_at_human": "1 week ago",
        "updated_at": "2024-01-21T10:05:00.000000Z"
    }
}
```

#### 5. Delete Task

**DELETE** `/tasks/{id}`

**Status:** `200 OK`

**Response:**

```json
{
    "message": "Task deleted successfully."
}
```
