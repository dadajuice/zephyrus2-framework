# Zephyrus — Application Template

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

The official application template for the [Zephyrus](https://github.com/zephyrus-framework/core) PHP framework. Start a new project with a working structure — controllers, Latte views, database, sessions, mailer, and Docker — all preconfigured.

---

## Quick Start

```bash
composer create-project zephyrus-framework/framework my-app
cd my-app
cp .env.example .env
docker compose up
```

Then open [http://localhost](http://localhost).

Install dependencies inside the container on first run:

```bash
docker exec -it zephyrus_webserver composer install
```

---

## Project Structure

```
app/
  Controllers/     Route controllers (auto-discovered via attributes)
  Models/          Domain models, services, brokers
  Views/           Latte templates
    layouts/       Layout templates
cache/
  latte/           Compiled template cache (auto-generated)
config.yml         Application configuration (YAML with !env tag support)
docker/            Docker service definitions
locale/            Translation files (JSON)
public/            Web root (Apache document root)
  index.php        Application entry point
  assets/          Static assets (CSS, JS, images, fonts)
sql/               Database initialization scripts
temp/              Temporary files
```

---

## Configuration

All configuration lives in `config.yml`. Sensitive values use the `!env` tag to reference environment variables from `.env`:

```yaml
database:
  host: !env DB_HOST, localhost
  password: !env DB_PASSWORD
```

Copy `.env.example` to `.env` and fill in your values. The example ships with working defaults for the Docker setup.

---

## Docker Environment

The included `docker-compose.yml` provides:

| Service | Port | Purpose |
|---|---|---|
| **PHP/Apache** | `80`, `443` | Application server (PHP 8.4) |
| **PostgreSQL** | `5432` | Database (auto-initialized from `sql/init.sql`) |
| **MailCatcher** | `1080` | Email testing UI (SMTP on `localhost:1025`) |

### Common Commands

```bash
# Start the environment
docker compose up

# Run composer inside the container
docker exec -it zephyrus_webserver composer install
docker exec -it zephyrus_webserver composer update

# Reset the database (removes volume, re-runs init.sql)
docker compose down -v
docker compose up
```

---

## Running Without Docker

If you prefer a local PHP setup:

```bash
composer install
php -S localhost:8080 -t public
```

You'll need PHP 8.4+ with `mbstring`, `pdo`, `intl`, and `sodium` extensions, and a PostgreSQL (or SQLite) database configured in `.env`.

---

## Controllers

Controllers use PHP 8 attributes for routing and are auto-discovered from `app/Controllers/`:

```php
use Zephyrus\Controller\Controller;
use Zephyrus\Routing\Attribute\Get;
use Zephyrus\Routing\Attribute\Post;
use Zephyrus\Http\Request;
use Zephyrus\Http\Response;

class ProductController extends Controller
{
    #[Get('/products')]
    public function index(): Response
    {
        return $this->render('products/index', [
            'products' => [],
        ]);
    }

    #[Post('/products')]
    public function store(Request $request): Response
    {
        $data = $request->body()->all();
        // ...
        return Response::redirect('/products');
    }
}
```

No manual route registration needed — just create a controller in `app/Controllers/` and add route attributes.

---

## Documentation

Full framework documentation is available on the docs site (coming soon). See the [core framework](https://github.com/zephyrus-framework/core) README for API details.

---

## License

MIT — see [LICENSE](LICENSE).
