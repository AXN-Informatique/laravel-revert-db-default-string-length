Laravel revert DB default string length
=======================================

    --------------------------------------
    /!\ UNFINISHED Do not use at this time
    --------------------------------------


This package revert database default string length to 255 characters in a Laravel project. It transforms all VARCHAR(191) columns to 255 characters.

This is especially useful for old projects that need to be updated.

Indeed, since Laravel 5.4 the default charset is "utf8mb4"; charset supported by MySQL from version 5.7.7
So, if the application was not running with this version at least, you had to put in the AppServiceProvider:

```php
Schema::defaultStringLength(191);
```

As a result, this package will be of great help to you to modernize an old application.

It proceeds in **X** steps:

1. ...
2. ...
3. ...

Instalation
-----------

Install the package with Composer:

```sh
composer require axn/laravel-revert-db-default-string-length
```

Usage
-----

First create a dump of your database in case there is a problem.

### Manualy

If you want to run the command directly:

```sh
php artisan revert-db-default-string-length:transform
```

### With migration

Pusblish the migration:

```sh
php artisan vendor:publish --tag="revert-db-default-string-length-migration"
```

So you can incorporate it into your deployment workflow with:

```
php artisan migrate
```
