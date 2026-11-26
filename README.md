# Appy — Complete Setup Guide

Appy is a Laravel app for building Android apps (WebView and WordPress) from a web dashboard. It includes plans and billing, payment gateways, an AI chat assistant, and a separate Android build server.

This guide covers running Appy with Docker, which is the recommended setup.

## Quick start

1. Install and open **Docker Desktop**.
2. In this folder, run:

   | Windows (PowerShell or cmd) | macOS / Linux |
   |---|---|
   | `.\appy.cmd start` | `./appy.sh start` |

3. Open **http://localhost:8080** and follow the installer (the database form is already filled in).

That's it. The script creates the settings files for you, builds the images and starts everything. If the Android build server folder (`../Builder`) is there, it starts that too and prints the key to enter in **Admin → Builders**.

Other commands (use `./appy.sh` instead of `.\appy.cmd` on macOS/Linux):

```
.\appy.cmd stop            # stop everything (data is kept)
.\appy.cmd update          # after code changes: rebuild, migrate, clear caches
.\appy.cmd status          # show container status
.\appy.cmd logs [service]  # follow logs (service: app, scheduler, builder, db)
```

The rest of this guide explains what happens under the hood and how to configure the admin area.

---

## 1. What runs where

`docker-compose.yml` starts four containers:

| Service | What it does | Reachable at |
|---|---|---|
| `app` | Laravel web app (PHP 8.3 + Apache) | http://localhost:8080 |
| `scheduler` | Laravel scheduler (build timeouts, notifications, credit resets, cleanup) | internal |
| `builder` | Android build server (Go + JDK 17 + Android SDK), built from `../Builder`. Only started when that folder exists. | internal: `http://builder:8845` |
| `db` | MySQL 8.4 | internal: `db:3306` |

Your data is kept in Docker volumes, so it survives rebuilds:

| Volume | Contents |
|---|---|
| `appy-mysql` | the database |
| `appy-storage` | uploads, logs, cached views, generated files |
| `appy-data` | the `.env` file written by the installer |
| `appy-builder-storage` / `appy-gradle-cache` | build output and the Gradle cache |

> ⚠️ **Never run `docker compose down -v`.** The `-v` flag deletes these volumes, and with them your database and uploads.

---

## 2. Requirements

- Docker Desktop (Windows/macOS) or Docker Engine with the Compose plugin (Linux)
- About 15 GB of free disk space (the Android SDK and Gradle cache are large)
- 4 GB or more of RAM for Docker (8 GB recommended while builds are running)
- This folder and, to build Android apps, the `Builder/` folder next to it:

```
some-folder/
├── Web-App-Builder/   ← this project
└── Builder/           ← Android build server (optional to start; needed to build apps)
```

If `Builder/` is somewhere else, set `APPY_BUILDER_PATH` in `appy.env`. Without it, the web app still runs, but builds can't be made.

---

## 3. First-time installation

The start script (`.\appy.cmd start` / `./appy.sh start`) does steps 3.1 to 3.3 for you. The manual steps are listed here for reference.

### 3.1 Settings file (`appy.env`)

The compose file reads variables that start with `APPY_`. They live in `appy.env`, which is created from `appy.env.example` on first start:

```env
APPY_URL=http://localhost:8080
APPY_PORT=8080
APPY_DB_DATABASE=appy
APPY_DB_USERNAME=appy
APPY_DB_PASSWORD=appy_secret
APPY_DB_ROOT_PASSWORD=root_secret
APPY_AUTO_MIGRATE=true
APPY_BUILDER_PATH=../Builder
```

For a production server, set your real `APPY_URL` and change both database passwords **before the first start** (the database is created with them).

### 3.2 Build server key (`docker/builder.env`)

`docker/builder.env` holds `BUILDER_KEY`. The start script creates it from `docker/builder.env.example` with a long random key. You will enter the same value in the admin panel later (step 4.2).

### 3.3 Build and start (manual equivalent)

```bash
docker compose --env-file appy.env up -d --build                     # web app only
docker compose --env-file appy.env --profile builder up -d --build   # also the Android build server
```

The first build takes a while (with the build server, it downloads the Android SDK). Check progress with:

```bash
docker compose ps
docker compose logs -f app
```

### 3.4 Run the web installer

1. Open **http://localhost:8080** (or your `APPY_URL`).
2. Follow the installer. The database form is already filled in for the Docker MySQL service:
   - Host: `db`, Port: `3306`
   - Database, username and password: the `APPY_DB_*` values (defaults: `appy` / `appy` / `appy_secret`)
3. Create the **admin account**. This is the super admin.

---

## 4. Admin configuration

Sign in with the admin account and open the **Admin** area.

### 4.1 Plans

**Admin → Plans**: create or edit plans (Free / Pro / Enterprise). For each plan you can set:
- monthly build credits (`-1` = unlimited)
- allowed builders
- custom CSS/JS
- API access
- hosted pages per app
- AI chat and monthly AI tokens
- whether users may use their own AI API key

> **Super admin access:** admins can activate any plan from **Choose Your Plan** without paying. The button reads "Activate … (Admin)". Admins also always have unlimited build credits, API access, hosted pages, and AI chat with their own key. For full access to every feature, activate the **Enterprise** plan once.

### 4.2 Build server

**Admin → Builders**: add a builder with:
- URL: `http://builder:8845`
- Server key: the `BUILDER_KEY` from `docker/builder.env` (the start script prints it)

### 4.3 Payment gateways

**Admin → Plugins / Payment Gateways**: turn on the gateways you use (for example Paystack, Razorpay, Crypto.com or Bank Transfer) and enter their keys. For Bank Transfer, enter the instructions customers will see. Admins are notified when a transfer is waiting for approval.

### 4.4 AI Chat Assistant

**Admin → Settings → AI**: turn on AI chat, choose a provider, paste the key, click **Test Connection**, then **Save**.

| Provider | Where to get a key | Notes |
|---|---|---|
| **OpenAI** | https://platform.openai.com/api-keys | Paid, billed per use. Models: GPT-4.1, GPT-4.1 Mini/Nano, GPT-4o. |
| **Anthropic (Claude)** | https://console.anthropic.com → API Keys | Paid, billed per use. Models: Claude Sonnet 4.6 (default), Opus 4.8, Haiku 4.5. A Claude Pro/Max subscription **cannot** be used here. You need an API key with credits. |
| **NVIDIA** | https://build.nvidia.com → open any model → **Get API Key** | Free trial. Models: Llama 3.3 70B (default), GPT-OSS 120B, Kimi K2, Nemotron Super 49B, Qwen3 Next 80B. |

Tips:
- The assistant uses tools to change apps for the user. If a model only chats and never takes action, pick another model. Llama 3.3 70B is the safest NVIDIA choice.
- Leave **Max tokens per request** at 4096 unless replies get cut off.

**Users' own keys:** if the user's plan allows it, users can open **AI Settings** in the chat and choose OpenAI, Anthropic or NVIDIA with their own key. Keys are stored encrypted. Usage with a user's own key doesn't count against the plan's token limit.

### 4.5 Email

Edit the mail settings in **Admin → Settings** (or the `MAIL_*` values in `.env`) so verification, password-reset and billing emails can be sent.

---

## 5. Updating the app after code changes

The code is copied into the Docker image, so you must rebuild after changing files. One command does it all:

```bash
.\appy.cmd update      # Windows
./appy.sh update       # macOS/Linux
```

This rebuilds the images, restarts the containers (the database is not touched), runs `php artisan migrate --force` and `php artisan optimize:clear`.

Manual equivalent:

```bash
docker compose --env-file appy.env build app
docker compose --env-file appy.env up -d app scheduler
docker compose exec app php artisan migrate --force
docker compose exec app php artisan optimize:clear
```

If old pages still appear, rebuild without the cache: `docker compose --env-file appy.env build --no-cache app`, then run `update` again.

To update the build server after changes in `../Builder`, run `update` too (it rebuilds the builder when the folder is present).

---

## 6. Everyday commands

```bash
docker compose ps                                   # status of all services
docker compose logs -f app                          # follow web app logs
docker compose logs -f builder                      # follow build server logs
docker compose exec app php artisan tinker          # Laravel console
docker compose exec app tail -f storage/logs/laravel.log
docker compose restart app                          # restart the web app
docker compose stop                                 # stop everything (data kept)
docker compose start                                # start again
```

---

## 7. Backups

**Database**

```bash
docker compose exec db sh -c 'mysqldump -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' > appy-backup.sql
```

**Restore**

```bash
docker compose exec -T db sh -c 'mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' < appy-backup.sql
```

**Uploads and .env**

```bash
docker run --rm -v appy_appy-storage:/data -v "$PWD":/backup alpine tar czf /backup/appy-storage.tgz -C /data .
docker run --rm -v appy_appy-data:/data    -v "$PWD":/backup alpine tar czf /backup/appy-data.tgz    -C /data .
```

(The project is named `appy`, so volume names always start with `appy_`, whatever this folder is called.)

---

## 8. Troubleshooting

| Problem | Fix |
|---|---|
| Port 8080 already in use | Set `APPY_PORT=8090` and `APPY_URL=http://localhost:8090` in `appy.env`, then run `start` again. |
| "Docker is not running" | Open Docker Desktop and wait until it says it's running. |
| Windows says scripts are disabled | Use `.\appy.cmd`, not `.\appy.ps1`. The `.cmd` file runs the script without changing your execution policy. |
| Script says no build server was found | Put the `Builder` folder next to this folder, or set `APPY_BUILDER_PATH` in `appy.env`, then run `start` again. |
| Installer can't connect to the database | Wait until `db` shows as healthy in `docker compose ps`. Use host `db`, not `localhost`. |
| Changes don't appear | Run `update` (section 5). |
| "Unknown column nvidia_api_key" | Run `update`, or `docker compose exec app php artisan migrate --force`. |
| Builds stay queued or fail at once | Check that the builder URL is `http://builder:8845` and the server key matches `BUILDER_KEY`. Check `docker compose logs builder`. |
| AI chat says the API key isn't configured | Set the provider and key in **Admin → Settings → AI** and save. |
| AI "Test Connection" fails | Check the key, the provider choice and the account credits. For NVIDIA, make sure the key was created at build.nvidia.com. |
| Emails aren't sent | Check the mail settings (section 4.5) and `storage/logs/laravel.log`. |
| 500 error | Run `docker compose exec app tail -n 100 storage/logs/laravel.log`. |

---

## 9. Production checklist

- [ ] Strong `APPY_DB_PASSWORD` and `APPY_DB_ROOT_PASSWORD` in `appy.env`, and a long `BUILDER_KEY` (the start script generates one)
- [ ] `APPY_URL` set to your real HTTPS domain
- [ ] A reverse proxy (Nginx, Caddy or Traefik) with HTTPS in front of port 8080
- [ ] `APP_DEBUG=false` in `.env` (the default)
- [ ] Mail configured and tested
- [ ] Regular database and storage backups (section 7)
- [ ] `APPY_AUTO_MIGRATE=true`, or always run migrations after updates
