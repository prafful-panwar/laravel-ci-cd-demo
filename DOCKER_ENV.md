# Docker Compose Configuration

This guide explains how to customize your Docker setup using environment variables.

## Quick Start

1. **Copy the example file:**

    ```bash
    cp .env.example .env
    ```

2. **Customize your settings** (optional - defaults work out of the box)

3. **Start the containers:**
    ```bash
    docker compose up -d
    ```

## Available Configuration Options

### Project Settings

| Variable               | Default          | Description                                                               |
| ---------------------- | ---------------- | ------------------------------------------------------------------------- |
| `COMPOSE_PROJECT_NAME` | `task-app`       | Prefix for all container names                                            |
| `RESTART_POLICY`       | `unless-stopped` | Container restart policy (`no`, `always`, `on-failure`, `unless-stopped`) |

### Web Server

| Variable      | Default        | Description                                            |
| ------------- | -------------- | ------------------------------------------------------ |
| `WEB_PORT`    | `8000`         | Port to access the app (e.g., `http://localhost:8000`) |
| `NGINX_IMAGE` | `nginx:alpine` | Nginx Docker image to use                              |

### Application

| Variable    | Default                 | Description                                                       |
| ----------- | ----------------------- | ----------------------------------------------------------------- |
| `APP_IMAGE` | `task-app-ci-cd-demo`   | Docker image name for the app                                     |
| `APP_ENV`   | `local`                 | Environment (`local`, `production`, `staging`)                    |
| `APP_DEBUG` | `true`                  | Enable debug mode (`true`/`false`)                                |
| `APP_URL`   | `http://localhost:8000` | Public URL of your application                                    |
| `APP_KEY`   | _(none)_                | Laravel encryption key (generate with `php artisan key:generate`) |

### Database

| Variable           | Default          | Description                                                  |
| ------------------ | ---------------- | ------------------------------------------------------------ |
| `DB_IMAGE`         | `mysql:8.0`      | MySQL Docker image (can use `mysql:5.7`, `mariadb:10`, etc.) |
| `DB_CONNECTION`    | `mysql`          | Database driver                                              |
| `DB_HOST`          | `db`             | Database container hostname                                  |
| `DB_PORT`          | `3306`           | Internal database port                                       |
| `DB_EXTERNAL_PORT` | `3307`           | Port to access DB from host machine                          |
| `DB_DATABASE`      | `task_app_ci_cd` | Database name                                                |
| `DB_USERNAME`      | `laravel`        | Database user                                                |
| `DB_PASSWORD`      | `password`       | Database password                                            |
| `DB_ROOT_PASSWORD` | `root`           | MySQL root password                                          |

## Example Configurations

### Development (Default)

Already set in `.env.example` - just copy and use!

### Production

```env
COMPOSE_PROJECT_NAME=myapp-prod
RESTART_POLICY=always
WEB_PORT=80
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
DB_PASSWORD=super-secure-password-here
DB_ROOT_PASSWORD=different-secure-password
```

### Multiple Instances on Same Server

```env
# Instance 1
COMPOSE_PROJECT_NAME=taskapp-staging
WEB_PORT=8001
DB_EXTERNAL_PORT=3308
DB_DATABASE=taskapp_staging

# Instance 2
COMPOSE_PROJECT_NAME=taskapp-dev
WEB_PORT=8002
DB_EXTERNAL_PORT=3309
DB_DATABASE=taskapp_dev
```

### Use Different Database

```env
# PostgreSQL instead of MySQL
DB_IMAGE=postgres:16
DB_CONNECTION=pgsql
DB_PORT=5432
DB_EXTERNAL_PORT=5433
```

## Common Scenarios

### Change the Port

If port 8000 is already in use:

```env
WEB_PORT=9000
APP_URL=http://localhost:9000
```

### Secure Passwords

For production, always change:

```env
DB_PASSWORD=your-secure-password-here
DB_ROOT_PASSWORD=different-secure-password
APP_KEY=base64:... # Generate with php artisan key:generate
```

### Custom Project Name

Useful when running multiple projects:

```env
COMPOSE_PROJECT_NAME=my-awesome-app
```

Containers will be named: `my-awesome-app-app`, `my-awesome-app-web`, `my-awesome-app-db`

## Testing Your Configuration

After updating `.env`, restart the containers:

```bash
docker compose down
docker compose up -d
```

Verify containers are running:

```bash
docker compose ps
```

Check logs if issues:

```bash
docker compose logs app
docker compose logs db
```

## Important Notes

⚠️ **Never commit your `.env` file** - it contains sensitive credentials  
✅ **Always use `.env.example`** as a template  
🔐 **Change default passwords** in production  
🔄 **Restart containers** after changing `.env`
