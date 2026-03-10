# Zephyrus 2 - Project Template

Template project for the [Zephyrus 2](https://github.com/dadajuice/zephyrus2) PHP framework.

## Prerequisites

- [Docker](https://www.docker.com/products/docker-desktop/) installed and running

## Quick Start

1. Copy the environment file:

```bash
cp .env.example .env
```

2. Start the development environment:

```bash
docker compose up
```

3. Install dependencies:

```bash
docker exec -it zephyrus_webserver composer install
```

4. Open [http://localhost](http://localhost) in your browser.

## Project Structure

```
app/
  Controllers/     Route controllers (auto-discovered via #[Route] attributes)
  Models/          Domain models, services, brokers
  Views/           Latte templates
    layouts/       Layout templates
cache/
  latte/           Compiled Latte template cache (auto-generated)
config.yml         Application configuration (YAML with !env tag support)
docker/            Docker service definitions
locale/            Translation files (JSON)
public/            Web root (Apache document root)
  index.php        Application entry point
  assets/          Static assets (images, fonts, etc.)
sql/               Database initialization scripts
temp/              Temporary files
```

## Configuration

All configuration lives in `config.yml` using YAML format. Sensitive values use the `!env` tag to reference environment variables from `.env`:

```yaml
database:
  host: !env DB_HOST
  password: !env DB_PASSWORD
```

## Database

PostgreSQL is included via Docker. The database is automatically initialized on first run using scripts in `sql/`.

To reset the database, remove the Docker volume and restart:

```bash
docker compose down -v
docker compose up
```

## MailCatcher

The development environment includes MailCatcher for testing emails. Access the web interface at [http://localhost:1080](http://localhost:1080).

SMTP is configured on `localhost:1025` with no authentication.

## Updating Dependencies

```bash
docker exec -it zephyrus_webserver composer update
```
