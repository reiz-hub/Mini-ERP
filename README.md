# Mini ERP System

A modern **Microservices-Based Enterprise Resource Planning (ERP) System** designed to manage business operations through independent, scalable, and maintainable services.

## Overview

Mini ERP is a distributed application built using a microservices architecture. Each service is responsible for a specific business domain and communicates through an API Gateway.

The system is designed to demonstrate:

* Microservices Architecture
* Service Isolation
* RESTful API Communication
* Containerization with Docker
* PostgreSQL Database Integration
* Authentication and Authorization
* Scalable Deployment

---

## System Architecture

```text
Client
   │
   ▼
API Gateway
   │
   ├── Auth Service
   ├── CRM Service
   ├── HR Service
   ├── Membership Service
   └── Reporting Service
```

### Services

#### API Gateway

Acts as the single entry point for all client requests.

Responsibilities:

* Request routing
* Authentication validation
* Load distribution
* Service communication

---

#### Auth Service

Handles user authentication and authorization.

Features:

* User Registration
* User Login
* Password Encryption
* JWT Authentication
* Role-Based Access Control

---

#### CRM Service

Manages customer information and interactions.

Features:

* Customer Management
* Customer Records
* Contact Information
* Relationship Tracking

---

#### HR Service

Handles employee-related operations.

Features:

* Employee Records
* Staff Management
* Department Information
* Human Resource Functions

---

#### Membership Service

Manages membership-related data and transactions.

Features:

* Membership Registration
* Membership Status Tracking
* Member Information Management

---

#### Reporting Service

Generates reports and analytics from collected system data.

Features:

* Business Reports
* Statistical Summaries
* Dashboard Data Aggregation
* Analytics Generation

---

## Technology Stack

### Backend

* PHP
* Laravel
* REST API

### Database

* PostgreSQL

### Containerization

* Docker
* Docker Compose

### Authentication

* JWT (JSON Web Token)

### Version Control

* Git
* GitHub

---

## Project Structure

```text
Mini-ERP/
│
├── api-gateway/
├── auth-service/
├── crm-service/
├── hr-service/
├── membership-service/
├── reporting-service/
│
├── docker/
├── docker-compose.yml
└── README.md
```

---

## Installation

### Clone Repository

```bash
git clone https://github.com/YOUR_USERNAME/Mini-ERP.git
cd Mini-ERP
```

### Configure Environment

Create and configure the required `.env` files for each service.

Example:

```env
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=database_name
DB_USERNAME=postgres
DB_PASSWORD=password
```

---

## Run Using Docker

Build and start all services:

```bash
docker compose up --build -d
```

View running containers:

```bash
docker ps
```

Stop all services:

```bash
docker compose down
```

---

## API Gateway

Default Access:

```text
http://localhost:8000
```

All requests should pass through the API Gateway.

---

## Database

PostgreSQL is used as the primary database system.

Each microservice may maintain its own database schema to ensure service independence and data isolation.

---

## Development Goals

* Improve scalability
* Reduce service coupling
* Enhance maintainability
* Support independent deployments
* Demonstrate enterprise architecture concepts

---

## Contributors

* Rizza Bombate
* Quennie Engracial
* Rodney Ignalague
* Anton Reblando
* Eden Carl Sausa

---

## Course Information

System Integration and Architecture

Mini ERP Project

---

## License

This project is developed for educational and academic purposes.
