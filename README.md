# Laravel Fast Setup

```
  ███████╗ █████╗ ███████╗████████╗    ███████╗███████╗████████╗██╗   ██╗██████╗
  ██╔════╝██╔══██╗██╔════╝╚══██╔══╝    ██╔════╝██╔════╝╚══██╔══╝██║   ██║██╔══██╗
  █████╗  ███████║███████╗   ██║       ███████╗█████╗     ██║   ██║   ██║██████╔╝
  ██╔══╝  ██╔══██║╚════██║   ██║       ╚════██║██╔══╝     ██║   ██║   ██║██╔═══╝
  ██║     ██║  ██║███████║   ██║       ███████║███████╗   ██║   ╚██████╔╝██║
  ╚═╝     ╚═╝  ╚═╝╚══════╝   ╚═╝       ╚══════╝╚══════╝   ╚═╝    ╚═════╝ ╚═╝
  Laravel Fast Setup — by Karim Kompissi
```

<div align="center">

[![Total Downloads](https://img.shields.io/packagist/dt/karimalik/laravel-fast-setup.svg?style=flat-square)](https://packagist.org/packages/karimalik/laravel-fast-setup)
[![Latest Version](https://img.shields.io/packagist/v/karimalik/laravel-fast-setup.svg?style=flat-square)](https://packagist.org/packages/karimalik/laravel-fast-setup)
[![PHP Version](https://img.shields.io/packagist/php-v/karimalik/laravel-fast-setup.svg?style=flat-square)](https://packagist.org/packages/karimalik/laravel-fast-setup)
[![License](https://img.shields.io/packagist/l/karimalik/laravel-fast-setup.svg?style=flat-square)](https://packagist.org/packages/karimalik/laravel-fast-setup)

</div>

Automates the most repetitive tasks when starting a new Laravel project — package installation, `.env` generation, and folder scaffolding — through an interactive CLI wizard.

---

## Installation

```bash
composer require karimalik/laravel-fast-setup
php artisan vendor:publish --tag=fast-setup-config
```

---

## Usage

### Full wizard

```bash
php artisan fast:setup
```

Guides you through all three steps interactively.

### Individual commands

```bash
php artisan fast:install-packages    # Select and install packages
php artisan fast:generate-structure  # Scaffold folder architecture
php artisan fast:generate-env        # Generate .env files per environment
```

### Options available on all commands

| Option | Description |
|---|---|
| `--dry-run` | Preview changes without applying anything |
| `--preset=name` | Run a named preset non-interactively (`fast:setup` only) |
| `--skip-interaction` | Skip top-level confirmation prompts (`fast:setup` only) |

---

## Presets

Run a full project setup in one command:

```bash
php artisan fast:setup --preset=api       # Sanctum, Horizon, Socialite, Backup — api structure
php artisan fast:setup --preset=standard  # Debugbar, Telescope, Livewire, Filament — standard structure
php artisan fast:setup --preset=ddd       # Permission, Activitylog, Horizon, Backup — ddd structure
```

Define your own in `config/fast-setup.php`:

```php
'presets' => [
    'my-preset' => [
        'name'      => 'My Stack',
        'packages'  => ['laravel/sanctum', 'laravel/cashier'],
        'structure' => 'api',
        'envs'      => ['local', 'staging', 'production'],
    ],
],
```

---

## Configuration

Edit `config/fast-setup.php` to add packages, folder structures, and presets.

### Adding a package

```php
'packages' => [
    'vendor/package' => [
        'name'         => 'Human-readable label',
        'post_install' => [
            'artisan' => 'package:install',  // optional
            'publish' => '--provider="..."', // optional
            'migrate' => true,               // optional
        ],
    ],
],
```

### Adding a structure

```php
'structures' => [
    'my-structure' => [
        'app/Services',
        'app/Repositories',
        'app/DTOs',
    ],
],
```

---

## License

MIT — [Karim Kompissi](https://github.com/karimalik)
