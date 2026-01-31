# EKS Cluster Access Setup Guide

## Problem

GitHub Actions can authenticate with AWS but cannot access the EKS cluster with kubectl.

**Error:**

```
error: You must be logged in to the server (the server has asked for the client to provide credentials)
```

## Solution

The GitHub Actions IAM user/role needs to be added to the EKS cluster's authentication configuration.

### Step 1: Get the IAM ARN

In the failing GitHub Actions workflow logs, look for the "Validate AWS credentials" step. You'll see:

```
Account: 123456789012
User/Role: arn:aws:iam::123456789012:user/your-user-name
```

Copy this ARN.

### Step 2: Add IAM User to EKS Cluster

Run this command **locally** (make sure you're authenticated as someone with EKS admin access):

```bash
eksctl create iamidentitymapping \
  --cluster task-app-cluster \
  --region us-east-1 \
  --arn "arn:aws:iam::123456789012:user/your-user-name" \
  --group system:masters \
  --username github-actions
```

Replace:

- `task-app-cluster` with your cluster name
- `us-east-1` with your region
- The ARN with the one from the workflow logs

### Step 3: Verify

```bash
eksctl get iamidentitymapping --cluster task-app-cluster --region us-east-1
```

You should see your GitHub Actions IAM user listed.

### Step 4: Re-run Workflow

Go to GitHub Actions → CD - Deploy to EKS → Re-run jobs

It should now work! ✅

---

## Alternative: Manual ConfigMap Edit

If you don't have `eksctl`, you can manually edit the `aws-auth` ConfigMap:

```bash
kubectl edit configmap aws-auth -n kube-system
```

Add this under `mapUsers`:

```yaml
mapUsers: |
    - userarn: arn:aws:iam::123456789012:user/your-user-name
      username: github-actions
      groups:
        - system:masters
```

Save and exit. The changes apply immediately.

---

## Why This Is Needed

EKS uses IAM for authentication, but authorization is managed by Kubernetes RBAC. The `aws-auth` ConfigMap maps IAM identities to Kubernetes users/groups.

By default, only the IAM user/role that **created** the cluster has access. GitHub Actions uses a different IAM user, so we need to explicitly grant it access.
