# How to Clone & Run FitLife ERP

This guide provides step-by-step instructions to clone, configure, and launch the **FitLife ERP** microservices system on your local machine.

---

## 🛠️ Prerequisites

Before you start, make sure you have the following installed on your machine:
*   **Git**: [Download Git](https://git-scm.com/)
*   **Docker Desktop**: Ensure Docker is installed and currently running.
*   **Port Availability**: Ensure the following ports are free:
    *   `8000` (API Gateway)
    *   `8001` - `8005` (Microservices)
    *   `3307` (MySQL Shared Database)

---

## 🚀 Setup Instructions

### Step 1: Clone the Repository

Clone the project from GitHub and navigate into the project directory:

```bash
# Clone using HTTPS
git clone https://github.com/reiz-hub/Mini-ERP.git

# Navigate into the project folder
cd Mini-ERP
```

---

### Step 2: Initialize Local Environment Files

Each microservice relies on a `.env` file for local configuration. Copy the template `.env.example` to `.env` for all 6 microservices:

#### 💻 Linux / macOS (Terminal)
```bash
for dir in api-gateway auth-service crm-service hr-service membership-service reporting-service; do
  cp "$dir/.env.example" "$dir/.env"
done
```

#### 💻 Windows (PowerShell)
```powershell
"api-gateway", "auth-service", "crm-service", "hr-service", "membership-service", "reporting-service" | ForEach-Object { Copy-Item "$_/.env.example" "$_/.env" }
```

#### 💻 Windows (Command Prompt)
```cmd
for %d in (api-gateway auth-service crm-service hr-service membership-service reporting-service) do copy %d\.env.example %d\.env
```

---

### Step 3: Build and Start the Containers

Launch the Docker Compose grid. The setup will automatically download base images, build each service's image, initialize database schemas, run migrations, and seed sample data:

```bash
docker compose up -d --build
```

---

### Step 4: Generate Application Encryption Keys

Laravel requires an unique application key (`APP_KEY`) for secure encryption. Run the key-generator command for each microservice container:

```bash
docker compose exec api-gateway php artisan key:generate
docker compose exec auth-service php artisan key:generate
docker compose exec crm-service php artisan key:generate
docker compose exec hr-service php artisan key:generate
docker compose exec membership-service php artisan key:generate
docker compose exec reporting-service php artisan key:generate
```

#### Restart the Services
Restart the containers once to ensure Laravel caches and uses the newly generated encryption keys:
```bash
docker compose restart
```

---

## 🔑 Accessing the System

Once all containers are running and keys are configured, open your web browser and visit:

👉 **[http://localhost:8000](http://localhost:8000)**

### 🔓 Default Administrative Credentials
*   **Email Address**: `test@example.com`
*   **Password**: `password`

---

## 🛠️ Diagnostics & Helpful Commands

*   **Check container status**:
    ```bash
    docker compose ps
    ```
*   **View real-time application logs**:
    ```bash
    docker compose logs -f
    ```
*   **Power down the system**:
    ```bash
    docker compose down
    ```
