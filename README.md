# Task App (Laravel + Vue)

A full-stack task management application built with Laravel 12 and Vue 3.

> [!NOTE]
> **About Project:** This repository is a reference implementation for **DevOps Best Practices** using Docker & Kubernetes. It demonstrates how to build, test, and deploy a Laravel/Vue application with a fully automated CI/CD pipeline.
>
> 🐳 **Docker Hub Integration:** Kubernetes manifests are configured to pull pre-built, optimized images directly from [Docker Hub](https://hub.docker.com/), ensuring fast and consistent deployments.

> [!TIP]
> **Smart Scaling:** This project features a custom "Cost-Optimized" configuration that allows the entire stack to run smoothly on a minimal **t3.small (2GB RAM)** instance, saving costs without sacrificing performance.

## Getting Started

You can run this application in **3 distinct ways**, depending on your needs.

<br>

---

## Option 1: Standard Local Setup (Clone & Install)

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

4.  **Access App**
    - Frontend: [http://127.0.0.1:8001](http://127.0.0.1:8001)
    - API: [http://127.0.0.1:8001/api/tasks](http://127.0.0.1:8001/api/tasks)

<br>
<br>

---

## Option 2: Local Docker Environment (Clone & Build)

_Best for: Developers who want an isolated containerized environment matching production._

1.  **Clone Repo**

    ```bash
    git clone https://github.com/prafful-panwar/task-app-ci-cd-demo.git
    cd task-app-ci-cd-demo
    ```

2.  **Start Containers**

    ```bash
    # 1. Copy Environment File
    cp -n .env.example .env

    # 2. Start and Build Containers
    docker-compose up -d --build

    # 3. Generate Application Key (Run inside container)
    docker-compose exec app php artisan key:generate
    ```

3.  **Access App**
    - Frontend: [http://localhost:8000](http://localhost:8000)
    - API: [http://localhost:8000/api/tasks](http://localhost:8000/api/tasks)

<br>
<br>

---

## Option 3: Pre-Built Production Image (Fastest)

_Best for: Deployment, demos, or testing without building code._

1.  **Using the Docker Hub Deployment Folder**
    Refer to the **[docker-hub-deployment](./docker-hub-deployment)** directory in this repository.

2.  **Quick Instructions**
    - Switch to that folder: `cd docker-hub-deployment`
    - Copy config: `cp .env.example .env`
    - Run: `docker-compose up -d`
    - Access at: [http://localhost:8100](http://localhost:8100)

<br>
<br>

---

## Option 4: Cloud Native / EKS (Production) ☁️

_Best for: Zero-Downtime deployments, auto-scaling, and high availability._

1.  **Infrastructure as Code:**
    This project includes full Kubernetes manifests for AWS EKS deployment.

2.  **Required Secrets:**
    To enable the automated pipeline, configure these Repository Secrets:

    | Secret Name             | Description                   | Used In Workflow               |
    | :---------------------- | :---------------------------- | :----------------------------- |
    | `AWS_ACCESS_KEY_ID`     | AWS Admin credentials for EKS | `.github/workflows/cd-eks.yml` |
    | `AWS_SECRET_ACCESS_KEY` | AWS Admin Secret Key          | `.github/workflows/cd-eks.yml` |
    | `AWS_REGION`            | Target AWS Region             | `.github/workflows/cd-eks.yml` |
    | `EKS_CLUSTER_NAME`      | Name of your EKS Cluster      | `.github/workflows/cd-eks.yml` |
    | `DOCKER_USERNAME`       | Docker Hub Username           | `.github/workflows/ci.yml`     |
    | `DOCKER_PASSWORD`       | Docker Hub Access Token       | `.github/workflows/ci.yml`     |

3.  **Features:**
    - **Zero-Downtime Rolling Updates**
    - **Automated Database Migrations** (K8s Jobs)
    - **Persistent Storage** (AWS EBS)
    - **Cost Optimized** (Runs on t3.small)

👉 **[View Full Kubernetes Guide](./k8s/README.md)**

<br>
<br>

---

## Development & Quality Assurance

### Run All Checks (Recommended)

Run the full quality assurance suite with a single command:

```bash
composer run code-shield
```

This runs the following checks in order:

1.  **Pint** (Fixes code style)
    `./vendor/bin/pint --test`

2.  **PHPStan** (Static Analysis)
    `./vendor/bin/phpstan analyse --memory-limit=2G`

3.  **Pest** (Unit/Feature Tests)
    `./vendor/bin/pest`

4.  **Type Coverage** (Enforces 100%)
    `php -d memory_limit=2G ./vendor/bin/pest --type-coverage`

5.  **ESLint** (Frontend Linting)
    `npm run lint`

### Git Hooks (Husky)

The `pre-commit` hook automatically runs the same shield command before every commit to ensure consistency.

```bash
composer run code-shield
```

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
        "updated_at": "2024-01-10T08:00:00.000000Z"
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
