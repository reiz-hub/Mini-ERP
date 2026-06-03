-- PostgreSQL initialization script for FitLife ERP microservices
-- This runs automatically when the postgres container starts for the first time.

-- Create all databases for FitLife ERP microservices
-- Note: db_auth is already created via POSTGRES_DB env var
CREATE DATABASE db_api_gateway;
CREATE DATABASE db_crm;
CREATE DATABASE db_membership;
CREATE DATABASE db_hr;
CREATE DATABASE db_reporting;
