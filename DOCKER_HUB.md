# Docker Hub Usage Guide

This project provides pre-built Docker images that allow you to run the application without cloning the source code.

**Images:**

- Application: `praffulpanwar2016/task-app-ci-cd-demo:latest`
- Web Server: `praffulpanwar2016/task-app-nginx:latest`

## Setup Instructions

To run the application, you need to create two files: `env` and `docker-compose.yml`.

### 1. Create a `.env` file

This file enables you to configure the host and port without modifying the Docker Compose file.

```ini
APP_HOST=localhost
APP_PORT=8100
```

### 2. Create a `docker-compose.yml` file

Copy the following content into a new `docker-compose.yml` file:

```yaml
version: '3.8'

services:
    app:
        image: praffulpanwar2016/task-app-ci-cd-demo:latest
        platform: linux/amd64
        restart: unless-stopped
        # Required for the entrypoint to locate artisan
        working_dir: /var/www
        depends_on:
            - db
        environment:
            # Uses variables from the .env file
            APP_URL: http://${APP_HOST}:${APP_PORT}
            CORS_ALLOWED_ORIGINS: http://${APP_HOST}:${APP_PORT}
            SANCTUM_STATEFUL_DOMAINS: ${APP_HOST}:${APP_PORT}
            # You can generate a new key or use this default for testing
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
            # Maps the port from .env (8100) to container port 80
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

Open your terminal in the directory where you created these files and run:

```bash
docker-compose up -d
```

The application will be accessible at `http://localhost:8100` (or the host/port you configured).
