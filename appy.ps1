# Appy helper for Windows. Run it through appy.cmd (works without changing the
# PowerShell execution policy):   .\appy.cmd start
#
#   start    first-time setup + build + start everything (safe to run again)
#   stop     stop all containers (data is kept)
#   update   rebuild after code changes, migrate the database, clear caches
#   status   show container status
#   logs     follow logs (optionally of one service: logs app | builder | db | scheduler)
param(
    [Parameter(Position = 0)][string]$Command = 'start',
    [Parameter(Position = 1)][string]$Service = ''
)
$ErrorActionPreference = 'Stop'
Set-Location -LiteralPath $PSScriptRoot

function Write-Step($msg) { Write-Host "==> $msg" -ForegroundColor Cyan }

function Get-Setting($name, $default) {
    $line = Get-Content appy.env | Where-Object { $_ -match "^\s*$name\s*=" } | Select-Object -Last 1
    if ($line) { return ($line -split '=', 2)[1].Trim().Trim('"') }
    return $default
}

function Invoke-Compose {
    & docker compose --env-file appy.env @script:Profiles @args
    if ($LASTEXITCODE -ne 0) { throw "docker compose $($args -join ' ') failed (exit code $LASTEXITCODE)" }
}

# --- checks and first-time files ---------------------------------------------
& docker info *> $null
if ($LASTEXITCODE -ne 0) { throw 'Docker is not running. Start Docker Desktop and try again.' }

if (-not (Test-Path appy.env)) {
    Copy-Item appy.env.example appy.env
    Write-Step 'Created appy.env with default settings (edit it to change port, URL or passwords).'
}

$builderPath = Get-Setting 'APPY_BUILDER_PATH' '../Builder'
$hasBuilder = Test-Path -LiteralPath (Join-Path $PSScriptRoot $builderPath)
$script:Profiles = @()
if ($hasBuilder) {
    $script:Profiles = @('--profile', 'builder')
    if (-not (Test-Path docker/builder.env)) {
        $bytes = New-Object byte[] 32
        [System.Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($bytes)
        $key = ($bytes | ForEach-Object { $_.ToString('x2') }) -join ''
        $text = (Get-Content docker/builder.env.example -Raw) -replace '(?m)^BUILDER_KEY=.*$', "BUILDER_KEY=$key"
        [System.IO.File]::WriteAllText((Join-Path $PSScriptRoot 'docker/builder.env'), $text, (New-Object System.Text.UTF8Encoding $false))
        Write-Step 'Created docker/builder.env with a random BUILDER_KEY.'
    }
}

$url = Get-Setting 'APPY_URL' 'http://localhost:8080'

# --- commands ----------------------------------------------------------------
switch ($Command) {
    'start' {
        if (-not $hasBuilder) {
            Write-Host "Note: no Android build server found at '$builderPath'. Starting the web app only." -ForegroundColor Yellow
            Write-Host "      Put the Builder folder there (or set APPY_BUILDER_PATH in appy.env) and run start again." -ForegroundColor Yellow
        }
        Write-Step 'Building and starting containers (the first run can take a while)...'
        Invoke-Compose up -d --build
        Write-Host ''
        Write-Host "Appy is starting at $url" -ForegroundColor Green
        Write-Host 'First time? Open that address and follow the installer (database host: db, port: 3306).'
        if ($hasBuilder) {
            $key = (Get-Content docker/builder.env | Where-Object { $_ -match '^BUILDER_KEY=' }) -replace '^BUILDER_KEY=', ''
            Write-Host 'Then add the build server in Admin -> Builders:'
            Write-Host '   URL:        http://builder:8845'
            Write-Host "   Server key: $key"
        }
    }
    'stop' { Invoke-Compose stop }
    'update' {
        Write-Step 'Rebuilding images...'
        Invoke-Compose build
        Write-Step 'Restarting containers...'
        Invoke-Compose up -d
        Write-Step 'Applying database changes...'
        & docker compose --env-file appy.env @script:Profiles exec app php artisan migrate --force
        if ($LASTEXITCODE -ne 0) { Write-Host 'Migrations skipped (run the installer first if the app is not installed yet).' -ForegroundColor Yellow }
        Write-Step 'Clearing caches...'
        Invoke-Compose exec app php artisan optimize:clear
        Write-Host "Done. Appy is running at $url" -ForegroundColor Green
    }
    'status' { Invoke-Compose ps }
    'logs' {
        if ($Service) { Invoke-Compose logs -f --tail 200 $Service } else { Invoke-Compose logs -f --tail 200 }
    }
    default {
        Write-Host 'Usage: .\appy.cmd [start|stop|update|status|logs [service]]'
        exit 1
    }
}
