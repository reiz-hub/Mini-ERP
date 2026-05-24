-- Create all databases for FitLife ERP microservices
CREATE DATABASE IF NOT EXISTS db_auth;
CREATE DATABASE IF NOT EXISTS db_api_gateway;
CREATE DATABASE IF NOT EXISTS db_crm;
CREATE DATABASE IF NOT EXISTS db_membership;
CREATE DATABASE IF NOT EXISTS db_hr;
CREATE DATABASE IF NOT EXISTS db_reporting;

-- Grant privileges to root user for all databases
GRANT ALL PRIVILEGES ON db_auth.* TO 'root'@'%';
GRANT ALL PRIVILEGES ON db_api_gateway.* TO 'root'@'%';
GRANT ALL PRIVILEGES ON db_crm.* TO 'root'@'%';
GRANT ALL PRIVILEGES ON db_membership.* TO 'root'@'%';
GRANT ALL PRIVILEGES ON db_hr.* TO 'root'@'%';
GRANT ALL PRIVILEGES ON db_reporting.* TO 'root'@'%';

FLUSH PRIVILEGES;
