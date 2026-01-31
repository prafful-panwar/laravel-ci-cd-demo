# Kubernetes Deployment for Task App

This directory contains Kubernetes manifests for deploying the Task App with **zero-downtime** rolling updates.

## Files

- `namespace.yaml` - Creates dedicated namespace for the app
- `mysql-pvc.yaml` - PersistentVolumeClaim for MySQL data
- `mysql-statefulset.yaml` - MySQL database with persistent storage
- `deployment.yaml` - App deployment with rolling update strategy
- `service.yaml` - LoadBalancer to expose the app

## Quick Start (Local Testing with minikube)

### 1. Install minikube and kubectl

```bash
# Mac
brew install minikube kubectl

# Start minikube
minikube start

# Verify
kubectl cluster-info
```

### 2. Deploy Application

```bash
# Apply all manifests
kubectl apply -f k8s/

# Watch deployment
kubectl get pods -n task-app --watch

# Get service URL
minikube service task-app-service -n task-app
```

### 3. Test Zero-Downtime Update

```bash
# Update to new image
kubectl set image deployment/task-app \
  app=praffulpanwar2016/task-app-ci-cd-demo:new-tag \
  -n task-app

# Watch rolling update (zero downtime!)
kubectl rollout status deployment/task-app -n task-app

# Verify pods (old pods gracefully replaced)
kubectl get pods -n task-app
```

### 4. Rollback if Needed

```bash
# Rollback to previous version
kubectl rollout undo deployment/task-app -n task-app

# Check rollout history
kubectl rollout history deployment/task-app -n task-app
```

## Deploy to AWS EKS

### Prerequisites

1. AWS CLI configured
2. eksctl installed
3. kubectl configured

### 1. Create EKS Cluster

```bash
eksctl create cluster \
  --name task-app-cluster \
  --region us-east-1 \
  --node-type t3.medium \
  --nodes 2 \
  --nodes-min 2 \
  --nodes-max 4 \
  --managed
```

This takes ~15-20 minutes.

### 2. Configure kubectl

```bash
aws eks update-kubeconfig --region us-east-1 --name task-app-cluster
```

### 3. Deploy Application

```bash
# Deploy all manifests
kubectl apply -f k8s/

# Get LoadBalancer URL
kubectl get service task-app-service -n task-app
```

### 4. Setup CI/CD

The GitHub Actions workflow will automatically deploy to EKS when you push changes.

Required GitHub Secrets:

- `AWS_ACCESS_KEY_ID`
- `AWS_SECRET_ACCESS_KEY`
- `AWS_REGION` (e.g., us-east-1)
- `EKS_CLUSTER_NAME` (e.g., task-app-cluster)

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

- **2 replicas** for zero-downtime during updates
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

## Cost Estimate (AWS EKS)

- EKS Control Plane: $0.10/hour (~$73/month)
- 2x t3.medium nodes: ~$60/month
- Load Balancer: ~$20/month
- **Total: ~$150/month**

### Free Tier Option (minikube/k3s)

For learning or testing, use minikube locally (free) or k3s on a single server.

## Troubleshooting

### Pods not starting

```bash
# Check pod status
kubectl describe pod <pod-name> -n task-app

# View logs
kubectl logs <pod-name> -n task-app
```

### Database connection issues

```bash
# Verify MySQL is running
kubectl get pods -n task-app | grep mysql

# Check MySQL logs
kubectl logs statefulset/mysql -n task-app
```

### LoadBalancer pending

```bash
# For minikube, use minikube tunnel in a separate terminal
minikube tunnel
```
