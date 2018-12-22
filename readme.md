# Connect your PTV application with Laravel

This package allows you to manage the import and export from data from and to the `transfer database` of an PTV application. This package uses the transferDB option within PTV to transfer data to and from PTV. Other options are NOT supported within this package.

Once installed you get access to following options:

> ````php
> // Add order to PTV
> new AddOrder($data);
> 
> // Update order to PTV
> new UpdateOrder($data);
>
> // Delete order in PTV
> new DeleteOrder($data);
> ````

You can also easily get exported routes from the PTV application using the functions:

> ````php
> // Fetch specific route
> $route = (new GetRoute())->fetch($id);
>
> // Fetch exports that are not imported
> $route = (new GetRoute())->getNotImported();
> ````

## Installation

This package can be used in Laravel 5.4 or higher. Lower versions are currently not supported.

You can install the package via composer:
>``
>composer require noxxie\ptv-laravel
>``

You can publish the config file like:
>``php artisan vendor:publish --provider="Noxxie\Ptv\PtvServiceProvider" --tag="config"``

When published, the config/ptv.php config file contains:
````php
return [

    /**
     * When the PTV library is used a seperate connection must be made to the PTV database
     * The database can be se specified within the config/database.php file the database connection name must be
     * specificied here. Default is "ptv", but as always you are free to change this to anything you want
     */

    'connection' => 'ptv',

    /**
     * The friendly naming config setting allows you to configure easier naming for specific columns in a specific table
     * this function can be helpfull when writing a import and you need to know what you are importing. With the original
     * column names of PTV that can be tricky.
     * 
     * Remember: for the classes "addOrder", "updateOrder", "deleteOrder" when you need to set a a value for a specific column
     * you can always use the originel column name, the class will figure it out that you want to set that specific column
     * 
     * When friendly names are specified the class expects that those values are used and not the original names
     */

    'friendly_naming' => [
        
        'IMPH_IMPORT_HEADER' => [
            'IMPH_REFERENCE' => 'id',
            'IMPH_EXTID' => 'reference'
        ],

        'IORH_ORDER_HEADER' => [
            'IORH_IMPH_REFERENCE' => 'id',
            'IORH_PRIORITY' => 'priority'
        ],

        'IORA_ORDER_ACTIONPOINT' => [
            'IORA_IMPH_REFERENCE' => 'id',
            'IORA_POSTCODE' => 'postcode',
            'IORA_CITY' => 'city',
            'IORA_COUNTRY' => 'country',
            'IORA_STREET' => 'street',
            'IORA_HOUSENO' => 'houseno',
            'IORA_HANDLINGTIME_CLASS' => 'timewindow',
            'IORA_EARLIEST_DATETIME' => 'from',
            'IORA_LATEST_DATETIME' => 'till'
        ],
    ],

    /**
     * The defaults settings are settings you can use to set default values for column data some have been already filled
     * for you as they are mendatory to be filled on insert. There is NO check if the fields do exist in de databse. 
     * Do NOT edit this if you are not 100% sure what you are doing
     */

    'defaults' => [

        /**
         * Default settings for the table IMPH_IMPORT_HEADER
         */
        
        'IMPH_IMPORT_HEADER' => [
            'IMPH_CONTEXT' => '1',
            'IMPH_OBJECT_TYPE' => 'ORDER',
            'IMPH_ACTION_CODE' => 'NEW',
            'IMPH_PROCESS_CODE' => '10',
            'IMPH_CREATION_TIME' => '%CURRENT_DATE%'
        ],

        /**
         * Default settings for the table IORH_ORDER_HEADER
         */

        'IORH_ORDER_HEADER' => [
            'IORH_ORDER_TYPE' => 'DELIVERY',
            'IORH_CODRIVER_NEEDED' => '0',
            'IORH_SOLO' => '0',
            'IORH_PRIORITY' => '1'
        ],

        /**
         * Default settings for the table IORA_ORDER_ACTIONPOINT
         */

        'IORA_ORDER_ACTIONPOINT' => [
            'IORA_ACTION' => 'DELIVERY',
            'IORA_IS_ONETIME' => '1',
            'IORA_CODRIVER_NEEDED' => '0',
            'IORA_TOUR_POS' => 'NONE'
        ]
    ]
];
````

The connection setting is important in this configuration. To allow this package to work correctly you need to add a extra database connection within your ``config/database.php`` file that matches your connection settings to the PTV transfer database.

By Default the ptv config files searches for a database connection with the name ``ptv`` but as always you are free to change it to everything you want.

# Usage


Every action you want to do with the PTV application has his own class and thus you need to add a different class.

## Orders

Orders are the main way to add new data into PTV.

### Add order

within your controller first add:
>````
>use Noxxie\Ptv\AddOrder;
>use Noxxie\Ptv\Helpers\getUniqueId;
>````

After that you have full access to the add order functionality within PTV. The helper ``getUniqueId`` helps you that you always insert a uniqueID into the database (This ID is only used to import data). Basic example for add a order:

````php
$data = collect([
    'id' => getUniqueId::generate(),
    'reference' => '12345678',
    'priority' => 1,
    'postcode' => '4761NV',
    'city' => 'zevenbergen',
    'street' => 'twintighoven',
    'houseno' => '41',
    'country' => 'NL',
    'timewindow' => '501',
    'from' => '20181222',
    'till' => '20181231'
]);

$add = new AddOrder();

if (!$add->create($collect)) {
    dd($add->getErrors()); 
}
else {
    $add->save();
}
````

Within your data array you can also specify the reall column name of data you want to insert. For example if you want to insert ``IORH_NUM_1`` you can do so by adding it to the array:

````php
$data = collect([
    'id' => getUniqueId::generate(),
    'reference' => '12345678',
    'priority' => 1,
    'postcode' => '4761NV',
    'city' => 'zevenbergen',
    'street' => 'twintighoven',
    'houseno' => '41',
    'country' => 'NL',
    'timewindow' => '501',
    'from' => '20181222',
    'till' => '20181231',
    'IORH_NUM_1' => 20
]);
````

The class will figure out for you that you want to add that custom value to that specific order.

**Note**: if you always want to add a extra value to the orders you can always add a friendly name in your ptv configuration (see install chapter for more information).

### Update order

within your controller first add:
>````
>use Noxxie\Ptv\UpdateOrder;
>use Noxxie\Ptv\Helpers\getUniqueId;
>````

After that you have full access to the update mechanism. The update mechanism works the same as the ``AddOrder`` class and has the same functionality. However, PTV transfer databse works when you use ``AddOrder`` with the same ``reference`` it will assume you want to do an update. 

The helper ``getUniqueId`` helps you that you always insert a uniqueID into the database (This ID is only used to import data).

To be sure you always add / update the correct data the best practice is to use ``AddOrder`` when adding a new order and to use ``UpdateOrder`` when updating an order.

PTV will give a error message when you want to update an order that does not exist. It will **NOT** throw an error when you want to update an existing order using ``AddOrder`` it will just add the order in PTV.

### Delete order

within your controller first add:
>````
>use Noxxie\Ptv\DeleteOrder;
>use Noxxie\Ptv\Helpers\getUniqueId;
>````

Afther that you have access to delete order programming. The helper ``getUniqueId`` helps you that you always insert a uniqueID into the database (This ID is only used to import data). An example of how to use this class:

````php
$data = collect([
    'id' => getUniqueId::generate(),
    'reference' => '12345678'
]);

$delete = new DeleteOrder();
if (!$delete->create($data)) {
    dd($delete->getErrors());
} else {
    $delete->save();
}
````

The delete class only needs to statements to work, an unique ID and the reference of the order you want to delete. Custom attributes are not needed within this class and thus are not supported.

## Routes

The route functionality add the capability to get information of routes that are exported from within the PTV application and reads the required data in to a ``collection``.

Because of the way how the export system works within PTV you only need one classes added within your controller:

>``
>use Noxxie\Ptv\GetRoute;
>``

After that you have access to a couple of functions how to retrieve routes from the database.

### getNotImported

With the ``getNotImported`` functionality you can retrieve all the exported data from PTV that is not imported in to your application or where you havent defined it as imported.

An example:
````php
$route = (new GetRoute())->getNotImported();
````

When no routes are found ``null`` is returned. When there is data a ``collection`` is returned.

You can add a parameter what action type you want to fetch. (``NEW``, ``UPDATE`` or ``DELETE``).

### fetch

You can also fetch a specified route number. This will return all the information regarding that specific route. If the specified route has more then one export all the exports will be returned.

The prefix for the routes are not enterd within this function and only the numeric value of the routenumbers are needed to find the information regarding the route.

When the route is notfound ``null`` is returned. When there is data a ``collection`` is returned.

Example:
```php
$route = (new GetRoute())->fetch(3885));
```

### updateRouteAsImported
When you are done importing a route into your application you can use the function ``updateRouteAsImported`` to mark the route as imported within the PTV database.

This makes sures when you call the ``getNotImported`` functionality you will not get the routes that are already imported in to your application.

Example:

```php
    $route = (new GetRoute())->updateRouteAsImported(3885);
```

This function will return ``true`` on succesfull update and ``false`` if anything did go wrong.