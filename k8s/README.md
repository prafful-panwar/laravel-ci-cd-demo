# Kubernetes Deployment for Task App

This directory contains Kubernetes manifests for deploying the Task App with **zero-downtime** rolling updates.

## Files

- `namespace.yaml` - Creates dedicated namespace for the app
- `mysql-pvc.yaml` - PersistentVolumeClaim for MySQL data
- `mysql-statefulset.yaml` - MySQL database with persistent storage
- `deployment.yaml` - App deployment with rolling update strategy
- `service.yaml` - LoadBalancer to expose the app

## 🐳 Image Source

This deployment uses public images hosted on **Docker Hub**:

- **App:** `praffulpanwar2016/task-app-ci-cd-demo`
- **Nginx:** `praffulpanwar2016/task-app-nginx`

> The CI/CD pipeline automatically builds new versions, pushes them to Docker Hub with a unique SHA tag, and updates the cluster.

## Resource Optimization Strategy (Low-Tier Server) 📉

This infrastructure is engineered to run a full production stack on a single **t3.small (2GB RAM)** node ($15/mo). Achieving "Zero-Downtime" on this minimal hardware required custom automated tuning:

1.  **Automated Bloat Removal:** The pipeline detects `t3.small` nodes and automatically deletes the K8s `metrics-server` (saving 400MB RAM) and scales down system drivers.
2.  **IP Address Management:** Forced `replicas: 1` to stay within the hardware limit of 3 Network Interfaces per node.
3.  **Strict Memory Budget:**
    - MySQL: 512MB
    - App: 512MB
    - System: 600MB
    - Headroom: ~400MB (Prevents OOM Kills)

> This proves that enterprise-grade Kubernetes architecture can be adapted for cost-effective implementation without sacrificing automation.

## Deployment Guide

### Important: IAM Access Warning ⚠️

**The IAM User who creates the cluster is automatically the ONLY Admin.**

- If you run `eksctl create` with your personal AWS Key, you MUST use those **SAME credentials** in GitHub Secrets (`AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`).
- If you use different credentials for GitHub Actions, the pipeline will fail with "Unauthorized".

### Prerequisites

1. AWS CLI configured
2. eksctl installed
3. kubectl configured

### 1. Create EKS Cluster (Cost-Optimized)

> **Note:** If you change the `--name` below, make sure to update it in all subsequent commands and GitHub Secrets.

```bash
eksctl create cluster \
  --name task-app-cluster \
  --region us-east-1 \
  --node-type t3.small \
  --nodes 1 \
  --with-oidc \
  --managed
```

**What this does:**

- **t3.small:** Cost-effective instance type.
- **--with-oidc:** Enables IAM roles for Service Accounts (Required for storage).
- **--managed:** Uses AWS Managed Node Groups for stability.

### 1.1 Enable Storage (EBS CSI Driver) 💾

**Critical Step:** Installing this addon allows your cluster to create Persistent Volumes (EBS) for the Database. Without this, MySQL will fail.

```bash
eksctl create addon \
  --name aws-ebs-csi-driver \
  --cluster task-app-cluster \
  --region us-east-1 \
  --force
```

_(Replace `task-app-cluster` with your actual cluster name if different)._

This setup takes ~15-20 minutes total.

### 2. Configure Local Access (Optional) 🖥️

Run this if you want to run `kubectl` commands from your local computer (debugging, checking pods).

```bash
aws eks update-kubeconfig --region us-east-1 --name task-app-cluster
```

### 3. Deploy (Automated via GitHub Actions) 🚀

You do **NOT** need to deploy manually.

1.  **Configure GitHub Secrets:**
    Go to `Settings` -> `Secrets and variables` -> `Actions` and add:

    | Secret Name             | Description                    | Example Value      |
    | :---------------------- | :----------------------------- | :----------------- |
    | `AWS_ACCESS_KEY_ID`     | AWS User Access Key (Admin)    | `AKIA...`          |
    | `AWS_SECRET_ACCESS_KEY` | AWS User Secret Key            | `wJalr...`         |
    | `AWS_REGION`            | AWS Region where cluster lives | `us-east-1`        |
    | `EKS_CLUSTER_NAME`      | Exact name of your EKS cluster | `task-app-cluster` |
    | `DOCKER_USERNAME`       | Docker Hub Username            | `myusername`       |
    | `DOCKER_PASSWORD`       | Docker Hub Access Token        | `dckr_pat_...`     |

2.  **Push Code:**
    ```bash
    git push
    ```
3.  **Done!** GitHub Actions will:
    - Build Docker Images
    - Run DB Migrations
    - Deploy/Update the App (Zero-Downtime)

## Key Features

### Zero-Downtime Rolling Updates

```yaml
strategy:
    type: RollingUpdate
    rollingUpdate:
        maxUnavailable: 0 # Always keep at least 1 pod running
        maxSurge: 1 # Max 1 extra pod during update
```

### Health Probes

**Readiness Probe:** Ensures new pods receive traffic only when ready
**Liveness Probe:** Automatically restarts unhealthy pods

### High Availability

- **Rolling Updates** for zero-downtime during updates
- **LoadBalancer** distributes traffic across healthy pods
- **StatefulSet** for MySQL with persistent storage

## Useful Commands

```bash
# Get all resources
kubectl get all -n task-app

# View pod logs
kubectl logs -f deployment/task-app -n task-app -c app

# Execute commands in pod
kubectl exec -it deployment/task-app -n task-app -- php artisan migrate

# Scale deployment
kubectl scale deployment/task-app --replicas=3 -n task-app

# Delete everything
kubectl delete namespace task-app
```

## Cost Estimate (AWS EKS - Optimized)

- **Node:** 1x t3.small (~$15/month)
- **EKS Control Plane:** ~$73/month (Standard AWS EKS fee)
- **Total:** ~$90/month (vs $150+ for medium/multi-node)

### Free Tier Option (minikube/k3s)

For learning or testing, use minikube locally (free) or k3s on a single server.

### 4. Need to Start Over? (Destroy Infrastructure) 🧨

To completely remove the cluster and stop all costs:

```bash
# 1. Delete the Cluster (Removes Nodes, volumes, load balancers)
eksctl delete cluster --name task-app-cluster --region us-east-1

# 2. (Optional) Delete Images from Docker Hub
# You might want to keep them, but if you want to save space:
# Visit https://hub.docker.com/repositories and manually delete tags.
```

## Troubleshooting

The CI/CD pipeline is designed to be self-healing. However, if you encounter issues:

- **Check Pod Status:** `kubectl get pods -n task-app`
- **View Logs:** `kubectl logs -l app=task-app -n task-app`
- **Reset Cluster:** See "Destroy Infrastructure" section above.
