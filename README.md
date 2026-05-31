# FitLife Mini-ERP 🏋️‍♂️

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Enabled-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![RabbitMQ](https://img.shields.io/badge/RabbitMQ-Event%20Driven-FF6600?style=for-the-badge&logo=rabbitmq&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

A production-grade microservice ERP system for fitness center management, built with **Laravel 11**, **Docker**, and **RabbitMQ**.

## 🏗️ Architecture Overview

The FitLife ERP breaks down business operations into 6 distinct microservices, communicating over HTTP and asynchronous RabbitMQ events.

*   **API Gateway**: The single entry-point serving Blade-rendered HTML views and orchestrating backend calls.
*   **Auth Service**: Centralized identity management and RS256 JWT generation.
*   **CRM Service**: Manages gym member demographics and profiles.
*   **Membership Service**: Handles subscriptions, billing dates, and plan catalog pricing.
*   **HR Service**: Manages active trainers and member-to-trainer assignments.
*   **Reporting Service**: Aggregates dashboard metrics from internal services.

> **Note:** For a deep dive into the system flow, request lifecycle, and JWT stateless authentication, please read our [ARCHITECTURE.md](ARCHITECTURE.md).

## 🚀 Quick Start (Docker)

To get the entire microservice grid running on your local machine, check out our step-by-step setup guide:

👉 **[Read the CLONE.md Guide](CLONE.md)**

### TL;DR

```bash
# 1. Clone the repository
git clone https://github.com/reiz-hub/Mini-ERP.git
cd Mini-ERP

# 2. Copy .env files (Linux/macOS)
for dir in api-gateway auth-service crm-service hr-service membership-service reporting-service; do cp "$dir/.env.example" "$dir/.env"; done

# 3. Generate RSA Keys for JWT Auth
bash docker/jwt-keys/generate-keys.sh

# 4. Start the grid (builds images, runs migrations & seeds)
docker compose up -d --build
```

Access the dashboard at **[http://localhost:8000](http://localhost:8000)**
*   **Email**: `test@example.com`
*   **Password**: `password`

## 🔐 Key Features

*   **Stateless Authentication**: Microservices verify JWT tokens locally using an RSA public key—no network round-trips to the Auth Service.
*   **Event-Driven Sync**: The Membership service publishes `MembershipActivated` events to RabbitMQ, which the CRM service consumes via background workers.
*   **Data Isolation**: Each microservice maintains its own independent database schema within the shared MySQL server (`db_auth`, `db_crm`, etc.).
