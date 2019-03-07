# PTV-Laravel

This package allows you to manage the import and export from data from and to the `transfer database` of an PTV application. This package uses the transferDB option within PTV to transfer data to and from PTV.

## Installation

This package can be used in Laravel 5.4 or higher.  Older versions are currently not supported.
You can install the package via composer:

`composer require noxxie/ptv-laravel`

The service provider will automatically get registered. Or you may manually add the service provider in your  `config/app.php`file:

````php
'providers' => [
     ...
     Noxxie\Ptv\PtvServiceProvider::class,
];
````

You can publish the config file with:
`php artisan vendor:publish --provider="Noxxie\Ptv\PtvServiceProvider" --tag="config"`

With the publication of the config file you change allot of default values as well add additional values to fit your needs. Reed the **config** for more information regarding configuration this package.

## Configuration
When the configuration file is published you can configure allot of settings using the configuration file.

`connection`, With this option you can specify what connection name this package needs to use in order to connect to your PTV transfer database. And so its *important* that you specify a extra databsae connection in your `config/database.php` file. By default this package will search for a connection with the name `ptv`.

Example of a database connection:
````php
...
'ptv'  =>  [
    'driver'            =>  'sqlsrv',
    'host'              =>  '',
    'port'              =>  '1433',
    'database'          =>  '',
    'username'          =>  '',
    'password'          =>  '',
    'charset'           =>  'utf8',
    'prefix'            =>  '',
    'prefix_indexes'    =>  true,
],
...
````
`friendly_naming`, This option allows you to use easier naming when writing imports for an order. I personally am always confused with what column name was used for which action and in what table it was stored.  With the friendly naming option you can "translate" the column names to a easier to remember name.

By default this package comes with pre configured easy naming options. For example when importing an order I wanted a easier naming for the address data so it made more sense for me:

````php
'IORA_ORDER_ACTIONPOINT'  =>  [
    'postcode'      =>  'IORA_POSTCODE',
    'city'          =>  'IORA_CITY',
    'country'       =>  'IORA_COUNTRY',
    'street'        =>  'IORA_STREET',
    'houseno'       =>  'IORA_HOUSENO',
    'timewindow'    =>  'IORA_HANDLINGTIME_CLASS',
    'from'          =>  'IORA_EARLIEST_DATETIME',
    'till'          =>  'IORA_LATEST_DATETIME',
],
````

Now with the above set in `friendly_naming` the package will automatically convert all the attributes to there correct column name.

`Defaults`, With this option you can set default values that are always required when you import an order but actually they never change when you want to create a new order. Instead of defining them with each import you can define them once inside the configuration class and they will be automatically injected in to your new order when you create one.

Please note that the default options are the first once to be set when you create a new order, and so you can overwrite every default option when you create a new order.

Also with the default option you can use `placeholders` that are replaced with data when you create an order. The following are available:

- `%UNIQUE_ID%`, will be replaced by a unique ID that is not used within the transfer database
- `%CURRENT_DATE%`, will be replaced by the current date (format Ymd)
- `%UNIQUE_IORA_ID%`, will be replaced by a unique ID that is used for creating new order locations within PTV

If you are **NOT** sure how to deal with this options with regard to the transfer database from PTV do **NOT** change the placeholders that are already in place by default within this package.

## Usage
This package can be used in different ways how to access the `order` or `route` instance. 
 
### Dependency injection
You can access one by dependency injection provided by Laravel:

````php
<?php
...
use Noxxie\Ptv\Order;
use Noxxie\Ptv\Route;

...

public function mockup(Route $route) {
    $order->create(...);
}

public function mockup_order(Order $order) {
    $route->getNotImported();
}
````
When you use the dependency injection option you will have an instance of the `route` or `order` object. And so you can directly use the functionality provided by that class.

### Resolving it from the service container
You can resolve the instances from the service container using:
````php
$route = App()->Make('Noxxie\Ptv\Route');
$order = App()->Make('Noxxie\Ptv\Order');
````

When you want to create a new order within PTV you can also resolve the instance from the service container and execute a functionality at once. For example: you want to create a new order within PTV you can resolve and execute a order at once:

````php
$order = App()->MakeWith('Noxxie\Ptv\Order', [
    'type' => 'CREATE',
    'attributes' => [...],
]);
````

The same applies for resolving a route from the service container. However the functionality is limited to fetch one route from the database.

````php
$route= App()->MakeWith('Noxxie\Ptv\Route', [
    'id' => 123456,
];
````

### Old fashion
Of course you can also use the old fashioned way and just create the class manually:
````php
<?php
...
use Noxxie\Ptv\Route;
use Noxxie\Ptv\Order;

...

public function mockup() {
    $Order = new Order();
    $Route = new Route();
}
````

## What's next?

Go use this package to create a awesome import to your PTV application. This package was created purely for the usage of my own project I am/was working on. However you are free to use this in any way you want to use it.

For more detailed examples and documentation look in the docs folder to get details about the [order](docs/order.md) and [route](docs/route.md) instances and what they can to exactly.

For the extra step into making sure your data is really imported into PTV you can checkout the [ImportCheck](docs/importcheck.md) functionality.

## License

The MIT License (MIT).
