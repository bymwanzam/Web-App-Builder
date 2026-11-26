#!/bin/sh
# Prepares persistent state, then runs the container command.
#
# The web installer writes .env and (optionally) database/database.sqlite at
# runtime, so both live in the /var/www/html/docker-data volume and are
# symlinked into place. storage/ is its own volume.
set -e

APP_DIR=/var/www/html
DATA_DIR="$APP_DIR/docker-data"
cd "$APP_DIR"

mkdir -p "$DATA_DIR"

# --- .env -------------------------------------------------------------------
if [ ! -f "$DATA_DIR/.env" ]; then
    echo "[appy] First start: creating .env from .env.example"
    cp .env.example "$DATA_DIR/.env"

    # Pre-fill values so the installer's database form points at the compose
    # MySQL service, and replace the shared example APP_KEY with a fresh one.
    php -r '
        [$_, $file] = $argv;
        $env = file_get_contents($file);
        $set = function ($key, $value) use (&$env) {
            if ($value === false || $value === "") return;
            if (preg_match("/[^A-Za-z0-9_.:\/+=-]/", $value)) {
                $value = chr(34) . addcslashes($value, chr(34) . chr(92)) . chr(34);
            }
            $line = $key . "=" . $value;
            $count = 0;
            $env = preg_replace_callback("/^" . $key . "=.*$/m", fn () => $line, $env, -1, $count);
            if ($count === 0) $env = rtrim($env) . "\n" . $line . "\n";
        };
        $set("APP_KEY", "base64:" . base64_encode(random_bytes(32)));
        $set("APP_URL", getenv("APP_URL"));
        $set("DB_HOST", getenv("INSTALL_DB_HOST"));
        $set("DB_PORT", getenv("INSTALL_DB_PORT"));
        $set("DB_DATABASE", getenv("INSTALL_DB_DATABASE"));
        $set("DB_USERNAME", getenv("INSTALL_DB_USERNAME"));
        $set("DB_PASSWORD", getenv("INSTALL_DB_PASSWORD"));
        file_put_contents($file, $env);
    ' "$DATA_DIR/.env"
fi
ln -sfn "$DATA_DIR/.env" "$APP_DIR/.env"

# --- SQLite (only used if SQLite is chosen in the installer) -----------------
[ -f "$DATA_DIR/database.sqlite" ] || touch "$DATA_DIR/database.sqlite"
ln -sfn "$DATA_DIR/database.sqlite" "$APP_DIR/database/database.sqlite"

# --- storage/ volume layout ---------------------------------------------------
mkdir -p storage/app/public storage/app/private storage/framework/cache/data \
         storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
[ -L public/storage ] || { rm -rf public/storage; ln -s ../storage/app/public public/storage; }

if [ "$(id -u)" = "0" ]; then
    chown -R www-data:www-data storage bootstrap/cache "$DATA_DIR" database
    chown www-data:www-data public
fi

# Drop any config cached from a previous run; the installer edits .env live.
php artisan config:clear > /dev/null 2>&1 || true

# Optional: run pending migrations on start (useful after upgrading the image).
if [ "${AUTO_MIGRATE:-false}" = "true" ]; then
    echo "[appy] Running migrations"
    setpriv --reuid=www-data --regid=www-data --init-groups php artisan migrate --force \
        || echo "[appy] Migrations skipped (app not installed yet or database unavailable)"
fi

# Apache starts as root and drops privileges itself; everything else runs as www-data.
if [ "$1" = "apache2-foreground" ] || [ "$(id -u)" != "0" ]; then
    exec "$@"
fi
exec setpriv --reuid=www-data --regid=www-data --init-groups "$@"
