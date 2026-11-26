# Appy — Complete Setup Guide

Appy is a Laravel app for building Android apps (WebView and WordPress) from a web dashboard. It includes plans and billing, payment gateways, an AI chat assistant, and a separate Android build server.

This guide covers running Appy with Docker, which is the recommended setup.

---

## 1. What runs where

`docker-compose.yml` starts four containers:

| Service | What it does | Reachable at |
|---|---|---|
| `app` | Laravel web app (PHP 8.3 + Apache) | http://localhost:8080 |
| `scheduler` | Laravel scheduler (build timeouts, notifications, credit resets, cleanup) | internal |
| `builder` | Android build server (Go + JDK 17 + Android SDK), built from `../Builder` | internal: `http://builder:8845` |
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
- This folder (`Install/`) and the `Builder/` folder next to it:

```
APPY/
├── Install/    ← this project
└── Builder/    ← Android build server
```

---

## 3. First-time installation

### 3.1 Configure the build server key

Edit `docker/builder.env` and set a long random value for `BUILDER_KEY`. You will enter the same value in the admin panel later (step 4.2).

### 3.2 (Optional) Change the defaults

The compose file reads variables that start with `APPY_`. To change them, create a file such as `appy.env`:

```env
APPY_URL=https://apps.example.com
APPY_PORT=8080
APPY_DB_DATABASE=appy
APPY_DB_USERNAME=appy
APPY_DB_PASSWORD=change-me
APPY_DB_ROOT_PASSWORD=change-me-too
APPY_AUTO_MIGRATE=true
```

Then pass it to every compose command with `--env-file appy.env`. For a production server, always change both database passwords.

### 3.3 Build and start

```bash
docker compose up -d --build
```

The first build takes a while because it downloads the Android SDK. Check progress with:

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
- Server key: the same `BUILDER_KEY` you set in `docker/builder.env`

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

The code is copied into the Docker image, so you must rebuild after changing files:

```bash
# 1. Rebuild the app image
docker compose build app

# 2. Restart the app and scheduler on the new image (the database is not touched)
docker compose up -d app scheduler

# 3. Apply database changes (needed after any update that adds a migration)
docker compose exec app php artisan migrate --force

# 4. Clear cached views, config and routes
docker compose exec app php artisan optimize:clear
```

If `APPY_AUTO_MIGRATE=true` is set, step 3 runs automatically when the container starts.

If old pages still appear, rebuild without the cache:

```bash
docker compose build --no-cache app
```

To update the build server after changes in `../Builder`:

```bash
docker compose build builder && docker compose up -d builder
```

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
docker run --rm -v install_appy-storage:/data -v "$PWD":/backup alpine tar czf /backup/appy-storage.tgz -C /data .
docker run --rm -v install_appy-data:/data    -v "$PWD":/backup alpine tar czf /backup/appy-data.tgz    -C /data .
```

(Volume names start with the folder name, here `install_`. Run `docker volume ls` to check them.)

---

## 8. Troubleshooting

| Problem | Fix |
|---|---|
| Port 8080 already in use | Set `APPY_PORT=8090` (or another free port) and run `docker compose up -d` again. |
| Installer can't connect to the database | Wait until `db` shows as healthy in `docker compose ps`. Use host `db`, not `localhost`. |
| Changes don't appear | Follow section 5 (rebuild, then `optimize:clear`). |
| "Unknown column nvidia_api_key" | Run `docker compose exec app php artisan migrate --force`. |
| Builds stay queued or fail at once | Check that the builder URL is `http://builder:8845` and the server key matches `BUILDER_KEY`. Check `docker compose logs builder`. |
| AI chat says the API key isn't configured | Set the provider and key in **Admin → Settings → AI** and save. |
| AI "Test Connection" fails | Check the key, the provider choice and the account credits. For NVIDIA, make sure the key was created at build.nvidia.com. |
| Emails aren't sent | Check the mail settings (section 4.5) and `storage/logs/laravel.log`. |
| 500 error | Run `docker compose exec app tail -n 100 storage/logs/laravel.log`. |

---

## 9. Production checklist

- [ ] Strong `APPY_DB_PASSWORD`, `APPY_DB_ROOT_PASSWORD` and `BUILDER_KEY`
- [ ] `APPY_URL` set to your real HTTPS domain
- [ ] A reverse proxy (Nginx, Caddy or Traefik) with HTTPS in front of port 8080
- [ ] `APP_DEBUG=false` in `.env` (the default)
- [ ] Mail configured and tested
- [ ] Regular database and storage backups (section 7)
- [ ] `APPY_AUTO_MIGRATE=true`, or always run migrations after updates
