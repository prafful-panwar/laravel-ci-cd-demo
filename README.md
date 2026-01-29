# Task App (Laravel + Vue)

A full-stack task management application built with Laravel 12 and Vue 3. Includes REST API, real-time task management, Docker support, and comprehensive testing.

## Getting Started

You can run this application in **3 distinct ways**, depending on your needs.

### Option 1: Standard Local Setup (Clone & Install)

_Best for: Code contributors, full control, standard development._

1.  **Clone & Install**

    ```bash
    git clone https://github.com/prafful-panwar/task-app-ci-cd-demo.git
    cd task-app-ci-cd-demo
    composer install
    cp .env.example .env
    php artisan key:generate
    npm install && npm run build
    ```

2.  **Configure Database**
    Update `.env` with your local database credentials:

    ```ini
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=db_name
    DB_USERNAME=root
    DB_PASSWORD=
    ```

3.  **Run Migrations & Server**
    ```bash
    php artisan migrate --seed
    php artisan serve --port=8001
    ```
    Access at: `http://127.0.0.1:8001/api/tasks`

---

### Option 2: Local Docker Environment (Clone & Build)

_Best for: Developers who want an isolated containerized environment matching production._

1.  **Clone Repo**

    ```bash
    git clone https://github.com/prafful-panwar/task-app-ci-cd-demo.git
    cd task-app-ci-cd-demo
    ```

2.  **Start Containers**

    ```bash
    # Copies .env.example to .env automatically if missing
    cp -n .env.example .env
    docker-compose up -d --build
    ```

3.  **Access App**
    - Frontend: [http://localhost:8000](http://localhost:8000)
    - API: [http://localhost:8000/api/tasks](http://localhost:8000/api/tasks)

---

### Option 3: Pre-Built Production Image (Fastest)

_Best for: Deployment, demos, or testing without building code._

1.  **Using the Docker Hub Deployment Folder**
    Refer to the **[docker-hub-deployment](./docker-hub-deployment)** directory in this repository.

2.  **Quick Instructions**
    - Switch to that folder: `cd docker-hub-deployment`
    - Copy config: `cp .env.example .env`
    - Run: `docker-compose up -d`
    - Access at: [http://localhost:8100](http://localhost:8100)

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
