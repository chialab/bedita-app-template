# BEdita Application Skeleton

A skeleton for creating applications with [BEdita](https://www.bedita.com) 5.x.

The framework source code can be found here: [bedita/bedita](https://github.com/bedita/bedita).

## Installation

1. Download [Composer](https://getcomposer.org/doc/00-intro.md) or update `composer self-update`.
2. Run `composer create-project --prefer-dist chialab/bedita-app-template [app_name]`.

If Composer is installed globally, run

```bash
composer create-project --prefer-dist "chialab/bedita-app-template:^5.0"
```

In case you want to use a custom app dir name (e.g. `/myapp/`):

```bash
composer create-project --prefer-dist "chialab/bedita-app-template:^5.0" myapp
```

You can now either use your machine's webserver to view the default home page, or start
up the built-in webserver with:

```bash
bin/cake server -p 8765
```

Then visit `http://localhost:8765` to see the welcome page.

## Update

Since this skeleton is a starting point for your application and various files
would have been modified as per your needs, there isn't a way to provide
automated upgrades, so you have to do any updates manually.

## Configuration

Read and edit `config/app.php` and setup the `'Datasources'` and any other
configuration relevant for your application.

## Testing

[![GitHub Actions tests](https://github.com/chialab/bedita-app-template/actions/workflows/php.yml/badge.svg?event=push&branch=main)](https://github.com/chialab/bedita-app-template/actions/workflows/test.yml?query=event%3Apush+branch%3Amain)
[![codecov](https://codecov.io/gh/chialab/bedita-app-template/branch/main/graph/badge.svg)](https://codecov.io/gh/chialab/bedita-app-template)

Test database configuration is in `app_local.php`. You can override the database url using the `DATABASE_TEST_URL` environment variable:

```bash
export DATABASE_TEST_URL='mysql://root:****@localhost/bedita4_app'
```

Then, you can launch tests using the `test` composer command:

```bash
composer run test
```
