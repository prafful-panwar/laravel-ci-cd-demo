# Manual Deployment & Testing Guide (Docker Image) 🐳

This setup allows you to run the application using the pre-built Docker Hub image, **without needing the original source code**.

It serves two main purposes:

1.  **Option 3 (Local Testing):** Quickly verify the "Production" image on your machine.
    - _Note:_ Works on Mac (Apple Silicon) via Docker's built-in emulation.
    - _Flexible:_ Since you have the config files locally, you can modify settings (ports, platform) if needed.
2.  **Manual Server Deployment:** Deploy to a VPS manually (Simulation of our CD Pipeline).

---

## 📋 Prerequisites

### A. Local Machine (Mac/Windows)

- **Requirement:** Install [Docker Desktop](https://www.docker.com/products/docker-desktop/).
- **Status:** Ensure Docker is running.

### B. Remote Server (Ubuntu / EC2)

You must install Docker before running deployment scripts.

```bash
# 1. Update & Install Docker
sudo apt update
sudo apt install -y docker.io

# 2. Start Service
sudo systemctl start docker
sudo systemctl enable docker

# 3. Add User to Docker Group
sudo usermod -aG docker $USER
newgrp docker

# 4. Install Docker Compose (Global)
sudo mkdir -p /usr/local/lib/docker/cli-plugins/
sudo curl -SL https://github.com/docker/compose/releases/latest/download/docker-compose-linux-x86_64 -o /usr/local/lib/docker/cli-plugins/docker-compose
sudo chmod +x /usr/local/lib/docker/cli-plugins/docker-compose
```

### C. Connection Check (Important) 🔍

Before deploying, verify that your server allows traffic on **Port 80** (AWS Security Groups / Firewall).

1.  **Run Test Container:** `docker run -d -p 80:80 --name test-web nginx`
2.  **Visit IP:** Go to `http://YOUR_SERVER_IP`
    - ✅ **See "Welcome to nginx"?** Great! Stop here and cleanup: `docker rm -f test-web`
    - ❌ **No connection?** Check your AWS Security Group > Inbound Rules > Add HTTP (Port 80) from `0.0.0.0/0`.
3.  **Cleanup:** You **MUST** run `docker rm -f test-web` to free up port 80 before proceeding.

---

## 🚀 Setup Instructions

Whether you are on **Localhost** or a **Remote Server**, the steps are identical.

### 1. Create a Deployment Folder

Create a folder (e.g., `mkdir task-app && cd task-app`).

### 2. Create Configuration Files

You need to create three files in that folder. Copy the content from the links below:

#### A. `docker-compose.yml`

> Defines the services (App, Nginx, DB) and pulls the images from Docker Hub.

1.  Create file: `nano docker-compose.yml`
2.  **[Copy content from here](./docker-compose.yml)**.
3.  Paste & Save.

#### B. `.env`

> Configures ports and database names.

1.  Create file: `nano .env`
2.  **[Copy content from here](./.env.example)**.
3.  **Edit:** Set `APP_PORT` (e.g., `80` for server, `8100` for local).

#### C. `deploy.sh`

> Automates volume creation, image pulling, and migrations.

1.  Create file: `nano deploy.sh`
2.  **[Copy content from here](./deploy.sh)**.
3.  Paste & Save.

### 3. Run Deployment

Make the script executable and run it:

```bash
chmod +x deploy.sh
./deploy.sh
```

---

## 🛠️ Helpful Commands

Since the application is containerized, use `docker compose exec app` to run commands inside the container.

### Run Database Seeder

Populate the database with dummy data (factories):

```bash
docker compose exec app php artisan db:seed
```

### Run Any Artisan Command

You can run standard Laravel commands:

```bash
# Clear Cache
docker compose exec app php artisan optimize:clear

# Open Tinker (Interactive Shell)
docker compose exec app php artisan tinker
```

---

---

## 🔐 Setting Up CI/CD Access (One-Time Setup)

To enable the **Automated** pipeline to deploy to this server, follow these steps:

### Step 1: Create a dedicated SSH key pair for GitHub Actions

- **Command:**
    ```bash
    ssh-keygen -t ed25519 -f ~/.ssh/github_actions_ec2 -C "github-actions-ci"
    ```
- **Comment:** This generates a private/public SSH key pair used _only_ by GitHub Actions to securely connect to the EC2 instance. (Press Enter for empty passphrase).
    - **Private key** → used by **GitHub** (Secret)
    - **Public key** → added to **EC2** (Authorized Keys)
    - **Key Fingerprint Example:** `SHA256:gjwyv2rDlQ8gsfrqcOSl+K+6BhiNO+cHu+dV/Jan+Pc github-actions-ci`

### Step 2: Add the public key to the EC2 instance

- **Commands on LOCAL:**

    ```bash
    # View the public key and copy for next step
    cat ~/.ssh/github_actions_ec2.pub
    ```

- **Commands on EC2:**

    ```bash
    nano ~/.ssh/authorized_keys
    # (paste the public key from previous step to authorized_keys file)

    chmod 700 ~/.ssh
    chmod 600 ~/.ssh/authorized_keys
    ```

 **Comment:** Registering the public key enables passwordless SSH access for CI/CD while enforcing correct SSH permissions.

- To manually verify SSH access from the machine that will run GitHub Actions, run (replace the someIpAddress with your `EC2_HOST`):

    ```bash
    ssh -i ~/.ssh/github_actions_ec2 ubuntu@someIpAddress
    ```

### Step 3: Store the private key in GitHub Secrets
- On the machine where you generated the SSH key, run the following to view the private key (do NOT share this publicly):

    ```bash
    # View the private key
    cat ~/.ssh/github_actions_ec2
    ```

- Go to: GitHub Repository → Settings → Secrets and variables → Actions and click on New repository secret and copy the entire private key exactly as printed and paste it into the `EC2_SSH_KEY` secret. Preserve its multiline format. Ensure the BEGIN and END header/footer lines each appear on their own line. For example:

    ```text
    -----BEGIN OPENSSH PRIVATE KEY-----
    <base64-encoded key data that spans multiple lines>
    -----END OPENSSH PRIVATE KEY-----
    ```

-  These secrets are injected into GitHub Actions at runtime and are never committed to the repository.

### Step 4: Add basic connection secrets

After adding the private key, create the remaining connection secrets:

- `EC2_HOST` → EC2 public IP or DNS name
- `EC2_USER` → `ubuntu`

---

## 🤖 Relationship to CD Pipelines

This manual process exactly mimics our **Automated CD Pipeline (Strategy A)**.

- **Manual:** You run these commands yourself.
- **Automated (Production):** GitHub Actions logs into your EC2 server and runs these exact commands automatically whenever you push code.

---

## 🧨 Need to Start Over? (Destructive Reset)

If a deployment fails (or you just want a fresh start) without destroying your entire EC2 server:

1.  **Stop & Remove Containers:**

    ```bash
    docker compose down
    ```

2.  **Delete Database Volume (Critical):**
    - Since the volume is `external`, `down -v` won't delete it. You must do it manually:

    ```bash
    docker volume rm task-app-dbdata
    ```

3.  **Deploy Again:**
    ```bash
    ./deploy.sh
