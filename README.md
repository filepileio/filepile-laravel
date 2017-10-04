# FilePile.io Laravel Integration Package

## Installation

Install into your project using:

```
composer require filepile/filepile-integration-laravel
```

If using Laravel older than 5.5, add to your config/app.php providers array:

```
FilePile\FilePileIntegration\Providers\FilePileIntegrationServiceProvider::class,
```

## Getting Started

1. [Create a FilePile.io account](https://filepile.io/register)
2. Create a new API Key (My Account > API Keys)
3. Copy your API Key to .env:

```
FILEPILE_KEY=YOUR-API-KEY
```

## Commands

This package offers a few simple commands to integrate with the filepile.io service:

### List

```
php artisan filepile:list
```

The list command will display all piles available for this api key. Each of these pile names are available to the "install" command

### Install

```
php artisan filepile:install {pile-slug}
```

The install command will install the given pile via it's slug. Slugs are created using the FilePile.io UI. Prompts will be displayed if assigned to the selected pile before files are copied.
