# UVDesk Community Edition - Deployment Steps

## Prerequisites
- Docker and Docker Compose installed on the target machine
- Git installed (to clone the repository)
- Port 8081 available (or modify as needed)
- Port 5434 available for PostgreSQL (optional, only if using external DB access)

## Deployment Steps

### 1. Clone the Repository
```bash
git clone [your-repository-url]
cd uvdesk-community-v1.1.7
```

### 2. Important File Modifications Made

#### Dockerfile Changes
- Added `ENV APP_ENV=prod` before composer install to prevent dev dependency issues
- Fixed the WebProfilerBundle loading issue in production
- Added PHP extensions: php8.1-gd, php8.1-curl, php8.1-mbstring, php8.1-intl, php8.1-zip for image processing and other features

#### compose.yaml Changes
- Changed uvdesk port from 8080 to 8081
- Changed PostgreSQL external port from 5432 to 5434
- Added `command: tail -f /dev/null` to keep container running
- Updated DATABASE_URL to use MySQL instead of PostgreSQL
- Added MySQL environment variables for automatic setup

#### Vendor Directory Fix
- Renamed `/vendor/uvdesk/mailbox-component/Utils/Imap` to `/vendor/uvdesk/mailbox-component/Utils/IMAP`
- Fixed namespace case sensitivity issues in IMAP-related files

### 3. Build and Run the Application

```bash
# Build the Docker image
docker compose build

# Start all services
docker compose up -d

# Check if containers are running
docker compose ps
```

### 4. Database Configuration

The application uses **MySQL** running inside the uvdesk container (not the PostgreSQL container).

#### MySQL Setup (Already configured)
The MySQL root password has been set to `uvdesk123`. These credentials are used:
- Host: `localhost` or `127.0.0.1`
- Port: `3306`
- Username: `root`
- Password: `uvdesk123`
- Database: `uvdesk`

### 5. Initial Setup via Web Interface

1. Access the application at: http://localhost:8081
2. Follow the setup wizard
3. Use the MySQL credentials above when prompted
4. Create your admin user account
5. Configure your organization details

### 6. Application Routes

- **Main Application**: http://localhost:8081
- **Admin Panel**: http://localhost:8081/member/login (or /en/member/login)
- **Customer Portal**: http://localhost:8081/en (front-end support center)

### 7. Environment Variables

Key environment variables in `.env`:
```env
APP_ENV=dev  # Change to 'prod' for production
DATABASE_URL=mysql://root:uvdesk123@127.0.0.1:3306/uvdesk
MAILER_URL=null://localhost  # Configure with your SMTP settings
```

### 8. Production Considerations

For production deployment:

1. **Change passwords**: 
   - Update MySQL root password
   - Change PostgreSQL password if using
   - Update APP_SECRET in compose.yaml

2. **Use HTTPS**: 
   - Add SSL certificates
   - Use a reverse proxy (nginx/traefik)

3. **Email Configuration**:
   - Update MAILER_URL with actual SMTP settings

4. **Backup Strategy**:
   - Regular MySQL database backups
   - Backup uploaded files in /var/www/uvdesk/public

5. **Monitoring**:
   - Set up container health checks
   - Monitor disk space for uploads
   - Set up logging aggregation

### 9. Useful Commands

```bash
# View logs
docker compose logs -f uvdesk

# Access uvdesk container shell
docker exec -it uvdesk-community-v117-uvdesk-1 bash

# Backup MySQL database
docker exec uvdesk-community-v117-uvdesk-1 mysqldump -u root -puvdesk123 uvdesk > backup.sql

# Clear Symfony cache
docker exec uvdesk-community-v117-uvdesk-1 php bin/console cache:clear

# Run database migrations
docker exec uvdesk-community-v117-uvdesk-1 php bin/console doctrine:migrations:migrate
```

### 10. Troubleshooting

**Container exits immediately**:
- Check logs: `docker compose logs uvdesk`
- Ensure the command in compose.yaml is `tail -f /dev/null`

**Database connection issues**:
- Verify MySQL is running: `docker exec uvdesk-community-v117-uvdesk-1 service mysql status`
- Check credentials match those in .env file

**Permission issues**:
- Fix var permissions: `docker exec uvdesk-community-v117-uvdesk-1 chmod -R 775 /var/www/uvdesk/var`
- Fix image cache permissions: `docker exec uvdesk-community-v117-uvdesk-1 chmod -R 775 /var/www/uvdesk/public/cache && docker exec uvdesk-community-v117-uvdesk-1 chown -R www-data:www-data /var/www/uvdesk/public/cache`

**Case mismatch errors**:
- Clear cache: `docker exec uvdesk-community-v117-uvdesk-1 rm -rf var/cache/*`
- Regenerate autoloader: `docker exec uvdesk-community-v117-uvdesk-1 composer dump-autoload`

## Notes
- The PostgreSQL container in compose.yaml is included but not actively used by default
- The application uses MySQL running inside the uvdesk container
- To switch to PostgreSQL, update the DATABASE_URL in .env file