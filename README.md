# Modules Pattern Template (PHP)

A minimal PHP project skeleton showcasing a simple Modules pattern with:
- PSR-4 autoloading
- Module-based routing (`/{module}/{action}`)
- Basic view rendering with layouts/components
- PostgreSQL database access via PDO
- Lightweight SQL migrations script


## Requirements
- PHP 8.1+ (PDO enabled)
- PDO PostgreSQL driver (php-pgsql)
- Composer
- PostgreSQL 13+


## Project Structure
```
app/
  config/
    config.php         # Loads env and exposes app-related settings
    db.php             # Loads DB settings from env variables
    env.php            # Tiny .env loader (reads app/.env)
  core/
    DB.php             # Singleton PDO connection factory (PostgreSQL)
    Helpers.php        # View renderer & small helpers
  modules/
    authentication/
      Logic.php        # Controller-like class for the module
      views/
        login.php      # View for Authentication module
    dashboard/
      Logic.php
      views/
        index.php
  views/
    components/        # Reusable view components
      button.php
      footer.php
      header.php
    layouts/           # Base layouts (main, auth, dashboard)
      auth.php
      dashboard.php
      main.php

composer.json          # PSR-4 autoload config
migrations/
  base.sql             # Optional baseline schema (manual apply if desired)
  up/                  # Forward (up) SQL migrations
    001_create_users.sql
  down/                # Rollback (down) SQL migrations
    001_drop_users.sql
public/
  index.php            # Front controller (router + dispatcher)
scripts/
  migrate.php          # Migrations runner (up/down)
vendor/                # Composer vendor directory
```


## Installation
1. Clone the repository
2. Install dependencies
   ```bash
   composer install
   ```
3. Create the environment file `app/.env` (see example below)
4. Ensure your database exists and credentials match the `.env`


## Environment (.env) Example
Create `app/.env` with at least the following variables:
```
# App
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
SESSION_COOKIE_NAME=app_session

# Database (PostgreSQL)
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=modules_template
DB_USERNAME=postgres
DB_PASSWORD=postgres
```
Notes:
- `app/config/env.php` loads this file at runtime (no external dotenv package required).
- `composer.json` configures PSR-4 for `Core\\`, `Modules\\`, `Components\\`, and `Config\\`.


## Database & Migrations
This project uses plain SQL files for migrations and a simple runner script.

- Configure DB via `app/.env` (see above).
- The migration log table name is `migration_log` (created automatically by the script).
- The script executes `.sql` files in `migrations/up` (ascending) and `migrations/down` (descending).

Run migrations:
```bash
# Apply all pending up migrations
php scripts/migrate.php up

# Rollback (apply down migrations that were previously applied)
php scripts/migrate.php down
```

Additional notes:
- `migrations/base.sql` is a convenience baseline you can apply manually if you want a starting schema. The runner does not automatically execute `base.sql`.
- Each migration file should be idempotent where possible, or ordered carefully to avoid errors.


## Running the Application (Built-in PHP server)
From the project root:
```bash
php -S localhost:8000 -t public
```
Then open `http://localhost:8000` in your browser.


## Routing & Modules
- URL pattern: `/{module}/{action}/{param1}/{param2}/...`
  - Default module: `dashboard`
  - Default action: `index`
- Dispatch flow:
  1. `public/index.php` parses the path.
  2. It builds the class `\\Modules\\{Ucfirst(module)}\\Logic`.
  3. It loads `app/modules/{module}/Logic.php` and calls the `{action}` method with the remaining segments as parameters.

Example:
- GET `/dashboard/index` → class `\\Modules\\Dashboard\\Logic`, method `index()`
- GET `/authentication/login` → class `\\Modules\\Authentication\\Logic`, method `login()` (if implemented)


## Views, Layouts, Components
- Views live under each module: `app/modules/{module}/views/*.php`
- Layouts live in `app/views/layouts`: choose layout by passing its name to the renderer
- Components live in `app/views/components` and can be included by views
- Rendering helper: `Core\\Helpers::render($viewPath, $data = [], $layout = 'main')`
  - Example usage from `Modules\\dashboard\\Logic::index()`


## Database Access
Use the shared PDO connection created in `public/index.php`:
```php
use Core\\DB;

$config  = require __DIR__ . '/../app/config/config.php';
$dbConfig = require __DIR__ . '/../app/config/db.php';
$pdo = DB::getInstance($dbConfig)->getConnection();
```
- A `PDO` instance is injected into each module `Logic` constructor by `public/index.php`.
- Errors: PDO is configured with exceptions (`ERRMODE_EXCEPTION`).


## Creating a New Module (Quick Start)
1. Create directory: `app/modules/blog/`
2. Add `Logic.php`:
   ```php
   <?php
   namespace Modules\\blog;

   use Core\\Helpers;
   use PDO;

   class Logic
   {
       public function __construct(private PDO $db, private array $config) {}

       public function index(): void
       {
           Helpers::render(__DIR__ . '/views/index.php', [
               'title' => 'Blog',
           ], 'main');
       }
   }
   ```
3. Add a view file: `app/modules/blog/views/index.php`
4. Visit: `/blog/index`


## Common Issues
- 404 Module not found: Ensure `app/modules/{module}/Logic.php` exists and the namespace is `Modules\\{module}` (lowercase folder, matching autoloading and router logic creates `ucfirst` for class).
- 404 Action not found: Add the method name to your `Logic` class or correct the URL.
- Database connection errors: Verify `.env` credentials and that the PostgreSQL service is running and reachable.
- Missing extensions: Ensure `ext-pdo` and `pdo_pgsql` are installed and enabled.


## Scripts
- `php scripts/migrate.php [up|down]`


## License
This template is provided as-is; add your preferred license.
