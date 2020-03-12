# Larakick 2.0

Create models (including migrations, factories and seeders), controllers, gates, requests, events and jobs with ease.

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

If you edit your YAML files, changes won't be automatically reflected in your project. To update your project, you can use this command:

    php artisan kickoff:update

This command will remove, modify and create any file depending on the changes, and save it in a versioning system. Alternatively, you can rollback any changes using `rollback`.

    php artisan kickoff:rollback

Finally, you can wipe clean all changes and start again using `remove`.

    php artisan kickoff:remove

## Generating your app

The whole documentation is on these files, since these covers a lot more than a simple README:

* [Models](wiki/MODELS.md): migrations, factories, seeders and JSON resources.
* [HTTP](wiki/HTTP.md): Controllers, routes, and internal logic (queries, events, jobs, notifications, validation requests, etc.)

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

Laravel is a Trademark of Taylor Otwell. Copyright © 2011-2020 Laravel LLC.
