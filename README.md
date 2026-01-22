# Task App (Laravel + Vue)

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

    The API will be available at: `http://127.0.0.1:8001/api/{endpoints}`

    _(Or if using Laravel Herd: `http://task-app-ci-cd.test/api/{endpoints}`)_

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

    Run the browser test inside the Docker container to verify frontend is working:

    ```bash
    docker-compose exec app php artisan test tests/Browser/LandingPageTest.php
    ```

    This test checks that the page loads and displays "Task Manager".

5.  **Stop Application**
    ```bash
    docker-compose down
    ```

## Using Docker Hub Image

If you want to use the pre-built Docker Hub image instead of building locally:

### Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running
- Internet connection to pull the image

### Quick Start with Docker Hub Image

1.  **Create a docker-compose file** (or use the verification folder):

    ```yaml
    version: '3.8'
    services:
        app:
            image: praffulpanwar2016/task-app-ci-cd-demo:latest
            restart: unless-stopped
            working_dir: /var/www
            environment:
                APP_URL: http://localhost:8080
                ASSET_URL: http://localhost:8080
                APP_KEY: base64:2fl+Ktvkfl+Frkxvry1vF+I5/1G5Q/5K5+555555555=
                APP_DEBUG: true
                DB_CONNECTION: mysql
                DB_HOST: db
                DB_PORT: 3306
                DB_DATABASE: task_app_test
                DB_USERNAME: laravel
                DB_PASSWORD: password
            depends_on:
                db:
                    condition: service_healthy
            networks:
                - task-app-network

        web:
            build:
                context: .
                dockerfile: Dockerfile.nginx
            restart: unless-stopped
            ports:
                - '8080:80'
            volumes:
                - .:/var/www
            depends_on:
                - app
            networks:
                - task-app-network

        db:
            image: mysql:8.0
            restart: unless-stopped
            environment:
                MYSQL_DATABASE: task_app_test
                MYSQL_USER: laravel
                MYSQL_PASSWORD: password
                MYSQL_ROOT_PASSWORD: root
            healthcheck:
                test: ['CMD', 'mysqladmin', 'ping', '-h', 'localhost']
                interval: 10s
                timeout: 5s
                retries: 5
            networks:
                - task-app-network

    networks:
        task-app-network:
            driver: bridge
    ```

2.  **Pull and run the image**:

    ```bash
    # Pull the image from Docker Hub
    docker pull praffulpanwar2016/task-app-ci-cd-demo:latest

    # Start all services
    docker-compose up -d
    ```

3.  **Access the application**:
    - **Frontend**: [http://localhost:8080](http://localhost:8080)
    - **API**: [http://localhost:8080/api/tasks](http://localhost:8080/api/tasks)

4.  **Verify it's working**:

    ```bash
    # Test API
    curl http://localhost:8080/api/tasks

    # View logs
    docker-compose logs app
    docker-compose logs web
    docker-compose logs db
    ```

5.  **Stop the application**:

    ```bash
    docker-compose down
    ```

### Image Details

- **Base Image**: PHP 8.4 FPM
- **Includes**: 
  - Laravel 12 framework
  - Vue 3 frontend
  - All dependencies pre-installed
  - Playwright browsers for testing
  - Database migrations & seeders run automatically
  
- **Database**: Automatically migrated and seeded on startup
- **Frontend Assets**: Pre-built and optimized

### Troubleshooting Docker Hub Image

**Port already in use?**
```bash
docker-compose down
# Change port in docker-compose.yml
# Then run again
docker-compose up -d
```

**Want to rebuild locally instead?**
```bash
# Use the local docker-compose.yml from the repo
git clone https://github.com/prafful-panwar/task-app-ci-cd-demo.git
cd task-app-ci-cd-demo
docker-compose up -d --build
```

**Clear Docker resources?**
```bash
docker-compose down -v  # -v removes volumes too
docker system prune -a
```



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
