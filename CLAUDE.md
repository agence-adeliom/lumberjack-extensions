# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Run tests
composer test

# Run tests with coverage
composer test-coverage

# Static analysis (PHPStan level 5)
vendor/bin/phpstan analyse

# Code style check (WPCS + PHPCS)
vendor/bin/phpcs

# Auto-fix code style
vendor/bin/phpcbf

# Automated refactoring (Rector)
vendor/bin/rector process

# Release a new version (updates composer.json version + CHANGELOG.md)
npm run release:patch   # bump patch version
npm run release:minor   # bump minor version
npm run release:major   # bump major version
```

## Architecture Overview

This is a PHP library (`agence-adeliom/lumberjack-extensions`) that extends the [Rareloop Lumberjack](https://github.com/Rareloop/lumberjack) WordPress framework with additional features. It is consumed as a Composer dependency by Lumberjack-based WordPress themes.

### Entry Point

`src/Extensions.php` is the main service provider that registers all sub-providers into the Lumberjack IoC container. Consumer themes register `Extensions::class` with `$app->register()`.

### Service Providers (`src/Providers/`)

Each provider bootstraps a feature area:

| Provider | What it does |
|---|---|
| `BlocksServiceProvider` | Auto-discovers and registers ACF Gutenberg blocks from `app/Blocks/` in the theme |
| `AdminServiceProvider` | Auto-discovers and registers ACF field groups from `app/Admin/` in the theme |
| `CronServiceProvider` | Registers WP cron jobs listed in `config/crons.php` |
| `HookServiceProvider` | Registers WordPress action/filter hooks listed in `config/hooks.php` |
| `EventServiceProvider` | Registers event listeners listed in `config/events.php` |
| `WebpackEncoreProvider` | Binds `WebpackEncore` instance using `config/webpack.php` directory |
| `TwigExtensionsServiceProvider` | Registers Twig extensions and allowed functions from `config/twig.php` |
| `EmailServiceProvider` | Lumberjack email support |
| `ValidationServiceProvider` | Lumberjack validation support |
| `RecaptchaServiceProvider` | Google reCAPTCHA integration |

### Key Abstractions

**Blocks (`src/Blocks/`)**
- `Block` — base class for ACF Gutenberg blocks. The block `$name` is derived from the PHP filename (PascalCase → kebab-case). Register ACF fields by overriding `registerFields()`.
- `AbstractBlock extends Block` — adds Timber/Twig rendering. Template path: `views/blocks/{name}.html.twig`. Preview image: `assets/images/admin/gutenberg-blocks/{name}/preview.jpg`. Block icon (SVG): `assets/images/admin/gutenberg-blocks/{name}/picto.svg`. Override `with()` to provide custom context fields to the template.
- `AbstractBlockPHP extends Block` — PHP-rendered variant (no Timber).
- `BlocksServiceProvider` auto-instantiates all classes in `app/Blocks/` that extend `AbstractBlock`, calls `init()` on each.

**Admin Field Groups (`src/Admin/`)**
- `AbstractAdmin` — defines ACF field groups. Must implement `getTitle()`, `getFields()`, and `getLocation()`. Supports ACF Options Pages via `hasOptionPage()`.
- `AdminServiceProvider` auto-discovers all classes in `app/Admin/` that extend `AbstractAdmin` and calls `::register()`.

**Post Types (`src/PostTypes/`)**
- `Post extends BasePost` — extends Lumberjack's post with ACF field auto-loading via `__get`/`__isset`, custom slug support, and a `paginate()` helper.
- `Term` — similar extension for taxonomy terms.
- `Page` — page-specific post type.

**Crons (`src/Crons/`)**
- `Cron` — abstract base. Set `$every` array with `seconds/minutes/hours/days/weeks/months` keys. Implement `handle()`. Register in `config/crons.php`.

**Flexible Layouts (`src/FlexibleLayout/`)**
- `AbstractLayout` — base for ACF Flexible Content layouts.

**Twig Extensions (`src/Twig/`)**
- `WebpackEncoreExtension` — exposes `webpack_asset()` Twig function for referencing Webpack Encore build assets.
- `RecaptchaExtension` — reCAPTCHA Twig helpers.
- Additional dev-only extensions registered in debug mode: `DumpExtension`, `BreakpointExtension`, `CommentedIncludeExtension`.

**Facades (`src/Facades/`)**
- `EventDispatcher`, `WebpackEncore` — Blast facades providing static access to container-bound instances.

### Config Files (`config/`)

These ship with the library as defaults. Consumer themes copy and override them:
- `hooks.php` — array of hook classes under `register` key
- `crons.php` — array of cron classes under `register` key
- `events.php` — event listener mappings
- `twig.php` — `allowed_functions` and `extensions` arrays
- `webpack.php` — `directory` key pointing to Webpack Encore build output (default: `"build"`)

### Versioning & Releases

Version is tracked in `composer.json`. The `npm run release:*` commands use `release-it` with `@release-it/bumper` to update `composer.json` and `@release-it/conventional-changelog` to update `CHANGELOG.md`. Commit format follows Angular conventional commits (`feat:`, `fix:`, `chore:`, etc.).
