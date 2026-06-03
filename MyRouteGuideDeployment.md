# Deployment Plan: Neon + Render (Laravel Microservices)

I noticed your request mentions `package.json` files and a clear "frontend/backend" split, which is typical for Node.js/React applications. However, since I already have full access to your `Mini-ERP-main` codebase, I know that your application is actually a **Laravel 11 Microservices architecture** consisting of 6 separate PHP applications orchestrated with Docker.

Because your architecture is advanced, deploying to Render requires a specific approach. Instead of a simple frontend/backend split, we will deploy **6 separate Web Services on Render**, all connected to a single **Neon PostgreSQL project**. 

Here is the tailored deployment plan based on your actual codebase:

## User Review Required

> [!WARNING]
> **Render Pricing:** Render charges per Web Service. Because your architecture requires 6 separate Web Services (`api-gateway`, `auth-service`, `crm-service`, `hr-service`, `membership-service`, `reporting-service`), deploying this architecture fully on Render will require at least 6 active instances. The free tier on Render is highly limited and will put instances to sleep, meaning your microservices will constantly timeout waiting for each other to wake up. **You will likely need paid Render instances for this to work reliably.**

> [!CAUTION]
> **Neon Databases:** Neon provides one default database (usually `neondb`). Your application requires 6 separate databases (`db_auth`, `db_crm`, etc.). We will need to manually create these 5 additional databases in your Neon console before deploying.

## Open Questions

1. **Are you prepared to run 6 separate Web Services on Render?** If you are aiming for a free/cheap deployment, microservices are very difficult to host. An alternative would be deploying everything on a single VPS (like DigitalOcean or Oracle Cloud) using your existing Docker Compose setup. Let me know if you prefer to stick to Render or pivot to a VPS.

## Proposed Changes / Phases

### Phase 1: Codebase Analysis & Repository Structure
- Your repository is a **Monorepo**. It contains 6 applications.
- **Frontend & Gateway:** The `api-gateway` folder serves the Blade UI (Frontend) and routes API requests.
- **Backends:** `auth-service`, `crm-service`, `hr-service`, `membership-service`, and `reporting-service`.
- **Render Strategy:** We will link your single GitHub repository to Render 6 times. For each Render service, we will use Render's **"Root Directory"** setting (e.g., `api-gateway`, `auth-service`) and instruct Render to build using the `Dockerfile` inside that directory.

### Phase 2: Database Migration (Neon PostgreSQL)
1. Create an account at [Neon.tech](https://neon.tech) and create a new Project.
2. In the Neon SQL Editor, execute `CREATE DATABASE` commands for `db_api_gateway`, `db_auth`, `db_crm`, `db_membership`, `db_hr`, and `db_reporting`.
3. Extract the primary connection string (e.g., `postgres://user:pass@ep-restless-snowflake.neon.tech/dbname?sslmode=require`).
4. We will format this into a `DATABASE_URL` environment variable for each Render service.
5. Render can automatically run your migrations during deployment, but because you are using Docker, we will update your `entrypoint.sh` scripts to ensure `php artisan migrate --force` runs automatically on boot.

### Phase 3: Git & GitHub Setup
- I will verify your `.gitignore` files ensure no `.env` files or vendor directories are committed.
- You will need to initialize git (if not already done) and push the entire `Mini-ERP-main` folder to a new GitHub repository.

### Phase 4: Render Deployment (The 5 Backend Services)
For each backend service (Auth, CRM, HR, Membership, Reporting):
1. **Type:** Web Service
2. **Environment:** Docker
3. **Root Directory:** e.g., `auth-service`
4. **Environment Variables:**
   - `DB_CONNECTION=pgsql`
   - `DB_URL` = (Your Neon connection string for that specific DB)
   - `APP_KEY` = (A newly generated base64 key)
   - `JWT_SECRET` (For auth-service only)
5. After deploying these 5, Render will assign them internal URLs (e.g., `http://auth-service:10000`). We will copy these URLs.

### Phase 5: Render Deployment (The API Gateway / Frontend)
1. **Type:** Web Service
2. **Environment:** Docker
3. **Root Directory:** `api-gateway`
4. **Environment Variables:**
   - Database credentials (same as backends)
   - `AUTH_SERVICE_URL` = (The internal Render URL from Phase 4)
   - `CRM_SERVICE_URL` = (The internal Render URL from Phase 4)
   - ...and so on for all 5 services.
5. Once deployed, Render will provide a public `.onrender.com` URL. This will be the only public-facing URL for your entire ERP.

## Verification Plan
- We will deploy the Auth service first as a test.
- We will check Render logs to ensure it connects to Neon and runs migrations successfully.
- We will deploy the remaining services.
- Finally, we will deploy the API Gateway and verify login functionality through the public Render URL.
