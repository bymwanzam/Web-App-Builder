#!/bin/sh
# Appy helper for macOS/Linux:   ./appy.sh start
#
#   start    first-time setup + build + start everything (safe to run again)
#   stop     stop all containers (data is kept)
#   update   rebuild after code changes, migrate the database, clear caches
#   status   show container status
#   logs     follow logs (optionally of one service: logs app | builder | db | scheduler)
set -e
cd "$(dirname "$0")"

step() { printf '\033[36m==> %s\033[0m\n' "$1"; }
setting() { v=$(grep -E "^[[:space:]]*$1[[:space:]]*=" appy.env | tail -n 1 | cut -d= -f2- | tr -d '"\r'); echo "${v:-$2}"; }

# --- checks and first-time files ---------------------------------------------
docker info > /dev/null 2>&1 || { echo "Docker is not running. Start Docker and try again." >&2; exit 1; }

if [ ! -f appy.env ]; then
    cp appy.env.example appy.env
    step "Created appy.env with default settings (edit it to change port, URL or passwords)."
fi

BUILDER_PATH=$(setting APPY_BUILDER_PATH ../Builder)
PROFILES=""
if [ -d "$BUILDER_PATH" ]; then
    PROFILES="--profile builder"
    if [ ! -f docker/builder.env ]; then
        KEY=$(od -An -tx1 -N32 /dev/urandom | tr -d ' \n')
        sed "s/^BUILDER_KEY=.*/BUILDER_KEY=$KEY/" docker/builder.env.example > docker/builder.env
        step "Created docker/builder.env with a random BUILDER_KEY."
    fi
fi

URL=$(setting APPY_URL http://localhost:8080)
compose() { docker compose --env-file appy.env $PROFILES "$@"; }

# --- commands ----------------------------------------------------------------
case "${1:-start}" in
    start)
        if [ -z "$PROFILES" ]; then
            printf '\033[33mNote: no Android build server found at %s. Starting the web app only.\n' "$BUILDER_PATH"
            printf '      Put the Builder folder there (or set APPY_BUILDER_PATH in appy.env) and run start again.\033[0m\n'
        fi
        step "Building and starting containers (the first run can take a while)..."
        compose up -d --build
        echo
        printf '\033[32mAppy is starting at %s\033[0m\n' "$URL"
        echo "First time? Open that address and follow the installer (database host: db, port: 3306)."
        if [ -n "$PROFILES" ]; then
            echo "Then add the build server in Admin -> Builders:"
            echo "   URL:        http://builder:8845"
            echo "   Server key: $(grep '^BUILDER_KEY=' docker/builder.env | cut -d= -f2-)"
        fi
        ;;
    stop)   compose stop ;;
    update)
        step "Rebuilding images..."
        compose build
        step "Restarting containers..."
        compose up -d
        step "Applying database changes..."
        compose exec app php artisan migrate --force \
            || echo "Migrations skipped (run the installer first if the app is not installed yet)."
        step "Clearing caches..."
        compose exec app php artisan optimize:clear
        printf '\033[32mDone. Appy is running at %s\033[0m\n' "$URL"
        ;;
    status) compose ps ;;
    logs)   shift; compose logs -f --tail 200 "$@" ;;
    *)      echo "Usage: ./appy.sh [start|stop|update|status|logs [service]]"; exit 1 ;;
esac
