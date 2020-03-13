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

First, publish the base YAML file. This command will put sample `kickoff/models.yml` and `kickoff/http.yml` file in your project root that will point other files.

    php artisan kickoff:create

Once you edit your YAML files, kick off the assistant with this artisan command.

    php artisan kickoff:start

If you edit your YAML files, changes won't be automatically reflected in your project. To update your project, you can use again `kickoff:start`

If you have [history](#history) enabled, you can rollback any changes to the previous state, including your YAML files.

    php artisan kickoff:undo
    
The above accepts rolling back a number of times using `--times=X` and going back to the first state using `--times=initial`.

> When starting again from a rollback, subsequent rollbacks are deleted. Think it as using "undo".

## Configuration

```php
<?php
return [
    'history' => false,
    'max' => 10,
    'path' => storage_path('larakick')
];
```

### History

Larakick can save a copy of your `kickoff`, `app`, `database` and `routes` directories in your storage folder before making changes. It's disabled by default, you can enable it by setting it to `true`.

### Maximum rollbacks

To avoid making the history uncontrollably bigger, you can set a maximum set of rollbacks. 

> This won't delete the initial state of your application, just before the first Larakick scaffold.

## Generating your app

The whole documentation is on these files, since these covers a lot more than a simple README:

* [Models](wiki/MODELS.md): migrations, factories, seeders and JSON resources.
* [HTTP](wiki/HTTP.md): Controllers, routes, and internal logic (queries, events, jobs, notifications, validation requests, etc.)
* [Authorization](wiki/AUTHORIZATION.md): Gates, policies, Form Requests with validation and authorization.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

Laravel is a Trademark of Taylor Otwell. Copyright © 2011-2020 Laravel LLC.
