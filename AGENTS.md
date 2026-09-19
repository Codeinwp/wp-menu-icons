# Menu Icons

## Overview

Menu Icons is a WordPress plugin for adding icons to navigation menus. PHP code is in `includes/`, JavaScript source is in `js/src/`, compiled browser assets are in `js/` and `css/`, and WordPress PHPUnit tests are in `tests/`.

## Environment

The Copilot setup workflow has already installed the locked Composer and npm dependencies, started MySQL, and installed the WordPress test suite. Do not rerun `composer install`, `npm ci`, or `bin/install-wp-tests.sh` unless the task changes their manifests or test setup.

The PHPUnit suite runs on PHP 7.4. The PHP coding-standards configuration maintains compatibility with older PHP versions.

## Validation

Run the available checks that apply to your change:

```bash
phpunit
composer run lint
```

Run `npm run build` only when JavaScript or style source changes require regenerated assets.

## Conventions

- Follow the project PHPCS configuration and the surrounding WordPress coding style.
- Edit JavaScript source in `js/src/`, then rebuild only when generated assets are required.
- Keep PHP changes compatible with the project's stated support range.
