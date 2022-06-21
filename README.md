# BGP API Builder

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bgpgroup/api-builder.svg?style=flat-square)](https://packagist.org/packages/bgpgroup/api-builder)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/nWidart/laravel-modules/master.svg?style=flat-square)](https://travis-ci.org/nWidart/laravel-modules)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/nWidart/laravel-modules.svg?maxAge=86400&style=flat-square)](https://scrutinizer-ci.com/g/nWidart/laravel-modules/?branch=master)
[![Quality Score](https://img.shields.io/scrutinizer/g/nWidart/laravel-modules.svg?style=flat-square)](https://scrutinizer-ci.com/g/nWidart/laravel-modules)
[![Total Downloads](https://img.shields.io/packagist/dt/bgpgroup/api-builder.svg?style=flat-square)](https://packagist.org/packages/bgpgroup/api-builder)


API Builder is a Laravel package for BGP system which allow to build Modules and Resources via commands. 

Using this package you can build:

- Modules
- Resources
- Collections
- Requests
- DTO's
- Controllers
- Models
- Tests

You can also create it all at once using module and resource commands

## Install

To install through Composer, by run the following command:

``` bash
composer require bgpgroup/api-builder
```

The package will automatically register a service provider and alias.

## Documentation

### Modules
To build a new module, by run the following command:

``` bash
php artisan bgp:make:module Sales
```

Where *Sales* will be the module name

This command will create:
- `src/Modules/Sales/Providers/AppServiceProvider.php`
- `src/Modules/Sales/Providers/AuthServiceProvider.php`
- `src/Modules/Sales/config/sales.php`
- `src/Modules/Sales/routes/api.php`

### Collections
Run the following command to build a collection:

```bash
php artisan bgp:make:collection Order --module=Sales
```
*Order* will be the resource name
*Sales* will be the module name

This command will create:
- `src/Modules/Sales/Collections/OrderCollection.php`

### Controllers
For controllers, run the following command:

```bash
php artisan bgp:make:controller Order --module=Sales
```
*Order* will be the resource name
*Sales* will be the module name

This command will create:
- `src/Modules/Sales/Controllers/OrderController.php`

### Migrations

Before run the command, you must setup the table columns

Edit `src/Modules/Sales/config/sales.php` adding 'resources' key like:

```php
<?php

return [

    'resources' => require_once('builder.php'),

    'roles' => [

    ],
];

```
And create a new php file `src/Modules/Sales/config/builder.php` with the following content as an example: 

```php
<?php

return [
    'Order' => [

        'migration' => [
            'table' => 'orders',
            'columns' => [
                'id' => true,
                'name' => [
                    'type' => 'string',
                    'size' => '100',
                ],
                'description' => [
                    'type' => 'text',
                    'nullable' => true,
                ],
                'amount' => [
                    'type' => 'double',
                    'size' => '100',
                    'default' => '0'
                ],
                'date' => [
                    'type' => 'dateTime',
                ],
                'client_id' => [
                    'type' => 'uuid',
                    'nullable' => true,
                ],
                'income' => [
                    'type' => 'enum',
                    'options' => "['on', 'off']",
                    'default' => 'on'
                ],
                'expenses' => [
                    'type' => 'enum',
                    'options' => "['on', 'off']",
                    'default' => 'on'
                ],
                'active' => true,
                'created_by' => true,
                'company_id' => true,
                'timestamps' => true,
                'soft_delete' => true
            ],
        ]
    ],
];
```

This config file allown you to generete the table columns in the migration file using the command:

```bash
php artisan bgp:make:migration Order --module=Sales
```
*Order* will be the resource name
*Sales* will be the module name

This command will create:
- `src/Modules/Sales/migrations/2022_06_20_212536_create_orders_table.php`

With the previous configuration, this will be the generated code:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->string('name', 100);
			$table->text('description')->nullable();
			$table->double('amount');
			$table->dateTime('date');
			$table->uuid('client_id')->nullable();
			$table->enum('income', ['on', 'off'])->default('on');
			$table->enum('expenses', ['on', 'off'])->default('on');
			$table->enum('active',['on', 'off'])->default('on');
			$table->foreignUuid('created_by')->nullable()->constrained('users');
			$table->foreignUuid('company_id');
			$table->timestamps();
			$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};

```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
