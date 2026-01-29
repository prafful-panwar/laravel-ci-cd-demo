# Quick Start Guide

This folder contains everything you need to run the Task App using pre-built Docker Hub images.

## Prerequisites

- Docker & Docker Compose installed.

## Setup Instructions

### 1. Configure Environment

Copy the example environment file:

```bash
cp .env.example .env
```

(Optional) Edit `.env` if you want to change the port (Default: 8100) or host.

### 2. Run Application

Start the containers:

```bash
docker-compose up -d
```

### 3. Access

The application will be running at:
[http://localhost:8100](http://localhost:8100) (or your configured port).
