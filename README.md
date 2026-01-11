# WordPress Project with OrbStack

This WordPress installation is configured to run with OrbStack (Docker) on macOS.

## Prerequisites

- [OrbStack](https://orbstack.dev/) installed on your Mac
- Docker Compose (included with OrbStack)

## Quick Start

1. **Start the containers:**
   ```bash
   docker compose up -d
   ```

2. **Access WordPress:**
   - Open your browser and go to: http://localhost:8080
   - Follow the WordPress installation wizard to complete setup

3. **Stop the containers:**
   ```bash
   docker compose down
   ```

4. **Stop and remove volumes (clean slate):**
   ```bash
   docker compose down -v
   ```

## Services

- **WordPress**: Available at http://localhost:8080
- **MySQL Database**: Running on port 3306 (internal only)

## Database Configuration

- **Database Name**: wordpress
- **Database User**: wordpress
- **Database Password**: wordpress
- **Database Host**: db (internal Docker hostname)

## Project Structure

- `docker-compose.yml` - Docker Compose configuration
- `wp-config.php` - WordPress configuration file
- `wp-content/` - WordPress themes, plugins, and uploads

## Useful Commands

- View logs: `docker compose logs -f`
- View WordPress logs: `docker compose logs wordpress -f`
- View database logs: `docker compose logs db -f`
- Access WordPress container: `docker compose exec wordpress bash`
- Access database: `docker compose exec db mysql -u wordpress -pwordpress wordpress`

## Notes

- Data persistence: Database data is stored in a Docker volume (`inoventis_db_data`)
- WordPress files: The WordPress installation is mounted from the current directory
- Port: Change the port in `docker-compose.yml` if 8080 is already in use

