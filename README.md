# Cloud-Native Task App (Laravel 12 + Vue 3) 🚀

**A Technical Proof-of-Concept for Docker, Kubernetes (EKS), and Zero-Downtime CI/CD Pipelines.**

> [!NOTE]
> 🎯 **Project Scope:** This is a **Technical Demonstration** of a full CI/CD lifecycle using Docker and Kubernetes. It serves as a proof-of-concept for Zero-Downtime deployments, automated testing, and infrastructure management on minimal resources.
>
> 🐳 **Docker Hub Integration:** Kubernetes manifests are configured to pull pre-built, optimized images directly from [Docker Hub](https://hub.docker.com/), ensuring fast and consistent deployments.

> [!TIP]
> ⚡ **Smart Scaling:** This project features a custom "Cost-Optimized" configuration that allows the entire stack to run smoothly on a minimal **t3.small (2GB RAM)** instance, saving costs without sacrificing performance.

## Getting Started (Development)

<br>

---

## Option 1: Standard Application (Local Code)

_Best for: Code contributors. Runs directly on your machine using standard tools._

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

---

## Option 2: Local Docker Build (Containerized)

_Best for: Developers needing isolation. Builds the application from your local code._

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

---

## Option 3: Manual Deployment (Pre-Built Images)

_Best for: Running the pre-built Docker Hub image (works for both Local Testing & Manual Server Deployment)._

1.  **Using the Docker Hub Deployment Folder**
    Refer to the **[docker-hub-deployment](./docker-hub-deployment)** directory in this repository.

    👉 **[View Manual Deployment Guide](./docker-hub-deployment/README.md)**

<br>
<br>

---

## Automated CI Pipeline (Testing & Build) 🧪

When you push code to GitHub (`git push`), the **CI Pipeline** runs automatically to ensure quality:

1.  **Code Quality:** Runs `Pint` (Style), `ESLint` (Frontend), and `PHPStan` (Static Analysis).
2.  **Testing:** Runs `Pest` (Unit/Feature Tests) and checks **Type Coverage**.
3.  **Build & Push:** Builds the production Docker Image from source and uploads it to Docker Hub (making it available for **Option 3** & **CD Pipelines**).

---

## Automated CD Pipelines (Deployment) 🚀

Once the CI Pipeline passes and the image is on Docker Hub, you have **two choices** for deployment:

### 🟢 Strategy A: Deployment to EC2 (Simple Docker)

_Effectively "Option 3" running on a server._

1.  **Pipeline:** `.github/workflows/cd.yml`
2.  **Mechanism:** SSH into server -> Pulls Docker Hub Image -> Restarts Container.
3.  **Result:** Simple, single-server deployment.

👉 **[View Server Deployment Guide](./docker-hub-deployment/README.md)**

### 🔵 Strategy B: Deployment to AWS EKS (Kubernetes)

_Cloud-Native orchestration using the same Docker Hub image._

1.  **Pipeline:** `.github/workflows/cd-eks.yml`
2.  **Mechanism:** Updates Kubernetes Manifests -> Triggers Rolling Update.
3.  **Result:** High Availability, Zero-Downtime, Auto-Scaling.

👉 **[View Full Kubernetes Guide](./k8s/README.md)**

### Required Secrets Configuration for CI/CD Pipelines

To use either pipeline, go to **Settings > Secrets and variables > Actions** in your repository and add these **GitHub Secrets**:

| Secret Name             | Description             | Used In Workflow File(s)         |
| :---------------------- | :---------------------- | :------------------------------- |
| `DOCKER_USERNAME`       | Docker Hub Username     | `ci.yml`, `cd.yml`, `cd-eks.yml` |
| `DOCKER_PASSWORD`       | Docker Hub Access Token | `ci.yml`                         |
| `AWS_ACCESS_KEY_ID`     | AWS Admin Keys          | `cd-eks.yml`                     |
| `AWS_SECRET_ACCESS_KEY` | AWS Admin Secret        | `cd-eks.yml`                     |
| `AWS_REGION`            | Target Region           | `cd-eks.yml`                     |
| `EKS_CLUSTER_NAME`      | EKS Cluster Name        | `cd-eks.yml`                     |
| `EC2_HOST`              | VM IP Address           | `cd.yml`                         |
| `EC2_USER`              | SSH Username            | `cd.yml`                         |
| `EC2_SSH_KEY`           | SSH Private Key         | `cd.yml`                         |

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
