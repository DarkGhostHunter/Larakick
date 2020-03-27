![Edoardo Busti - Unsplash #2QwMsZ1TIdI](https://images.unsplash.com/photo-1508087625439-de3978963553?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1280&q=80&h=400)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/darkghosthunter/larakick.svg?style=flat-square)](https://packagist.org/packages/darkghosthunter/larakick) [![License](https://poser.pugx.org/darkghosthunter/larakick/license)](https://packagist.org/packages/darkghosthunter/larakick)
![](https://img.shields.io/packagist/php-v/darkghosthunter/larakick.svg)
 ![](https://github.com/DarkGhostHunter/Larakick/workflows/PHP%20Composer/badge.svg)
[![Coverage Status](https://coveralls.io/repos/github/DarkGhostHunter/Larakick/badge.svg?branch=master)](https://coveralls.io/github/DarkGhostHunter/Larakick?branch=master)

# Larakick  

Scaffold your project models, database, controllers, requests, events and jobs with ease and flexibility.

**How does it work?**

Larakick reads one or many YAML files to create multiple related files in your project: models, migrations, factories, seeders, routes, permissions, policies, policies, events, jobs and requests.

## Requirements:

* Laravel 7
* Be lazy 

## Install:

Install this package using Composer directly to your development packages.

```bash
composer require darkghosthunter/larakick --dev
```
	
## Usage

First, publish the sample YAML files, `larakick/models.yml` and `larakick/http.yml`, in your project root.

    php artisan larakick:sample

Once you edit your YAML files, kick off the assistant with this artisan command.

    php artisan larakick:scaffold

### Safety first

Larakick will automatically copy your `app`, `routes` and some of your `database` directories as backups every time you scaffold.

You can find it in your application default storage path under the `larakick/backups` directory, and copy them over your project file. 

## Generating your app

The whole documentation is on these files, since these covers a lot more than a simple README:

* [Database](wiki/DB.md): migrations, factories, seeders and JSON resources.
* [HTTP](wiki/HTTP.md): Controllers, middlewares, routes, and internal logic (queries, events, jobs, notifications, validation requests, etc.)
* [Authorization](wiki/AUTH.md): Gates, policies, Form Requests with validation and authorization.

Just remember the basics:

* Method chaining is done using spaces: `query: Post whereNotNull:published_at paginate`.
* Methods receive arguments using `:` colon: `query: whereNotNull:published_at`.
* And RTFM.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

Laravel is a Trademark of Taylor Otwell. Copyright © 2011-2020 Laravel LLC.
