# Order

With the order instance you can `create`, `update` and `delete` an order within PTV.

## Initiate an order object

You can initiate a order object in different ways

### Dependency injection
You can access one by dependency injection provided by Laravel:

````php
<?php
...
use Noxxie\Ptv\Order;
...

public function mockup(Order $order) {
    $order->create(...);
}
````
When you use the dependency injection option you will have an instance of the `order` object. And so you can directly use the functionality provided by that class.

### Resolving it from the service container
You can resolve the instances from the service container using:
````php
$order = App()->Make('Noxxie\Ptv\Order');
````

When you want to create a new order within PTV you can also resolve the instance from the service container and execute a functionality at once. For example: you want to create a new order within PTV you can resolve and execute a order at once:

````php
$order = App()->MakeWith('Noxxie\Ptv\Order', [
    'type' => 'CREATE',
    'attributes' => [...],
]);
````

### Using the facade
You  can also use the provided facade to get access to the order instance.
````php
<?php
...
use Noxxie\Ptv\Facades\Order;
...

public function mockup() {
    $Order = Order::create(...);
}
````

## Methods

The order object has three methods you can use to control the data flow within PTV.

### Create(array $attributes)

With the create method you can create a new record or records within the PTV transfer database. When you want to create one record you can define a flat array. If you want to import multi orders at once you can provide a multidimensional array. 

A simple example for creating one valid order:
````php
...
use Noxxie\Ptv\Facades\Order;
...

$data = [
    'reference'     => '987654321',
    'priority'      => 1,
    'postcode'      => '3744AA',
    'city'          => 'Baarn',
    'street'        => 'Amsterdamsestraatweg',
    'houseno'       => '1',
    'country'       => 'NL',
    'timewindow'    => '501',
    'from'          => '20190101',
    'till'          => '20191231',
    'IORH_TEXT_1'   => 'Hello world',
];

$order = order::create($data);
$order->save();
````

When you want to create more orders at once you create the array like:
````php
...
use Noxxie\Ptv\Facades\Order;
...

$data = [
    [
        'reference'     => '987654321',
        'priority'      => 1,
        'postcode'      => '3744AA',
        'city'          => 'Baarn',
        'street'        => 'Amsterdamsestraatweg',
        'houseno'       => '1',
        'country'       => 'NL',
        'timewindow'    => '501',
        'from'          => '20190101',
        'till'          => '20191231',
        'IORH_TEXT_1'   => 'Hello world',
    ],
    [
        'reference'     => '12345678',
        'priority'      => 1,
        'postcode'      => '3744AA',
        'city'          => 'Baarn',
        'street'        => 'Amsterdamsestraatweg',
        'houseno'       => '1',
        'country'       => 'NL',
        'timewindow'    => '501',
        'from'          => '20190101',
        'till'          => '20191231',
        'IORH_TEXT_1'   => 'Hello world',
    ],
];

$orders = order::create($data);
$orders->save();
````

In combination with the configuration option friendly naming the above attributes are easy to read and to understand what data goes were. (See the configuration section to view this functionality).

When you did not specify a friendly naming option you an use the raw column name to add data to the order. In the above example `IORH_TEXT_1` does not have a friendly_name configured and so the raw column name is used to defined the import data.

**Caution** the create order does not check if the order exists within PTV. If the order with the same `reference` already exists within PTV it will **UPDATE** the order instead of creating a new one. This is the way the import in PTV works.

### Update(array $attributes)

With the update method you can update a existing order within PTV. When you want insert one record you can define a flat array. If you want to import multi orders at once you can provide a multidimensional array. 

The implementation of the methods `update` and `create` are exactly the same the only difference is that the column name `IMPH_ACTION_CODE` is set to `UPDATE` instead of `NEW`.

**Caution** the update order does not check if the order exists within PTV. If the order does **NOT** exist within PTV it will create a new order instead. This is the way PTV works.

### Delete(array $attributes)

When you want to delete an existing order within PTV you can use the Delete method. When you want to create one record you can define a flat array. If you want to import multi orders at once you can provide a multidimensional array. 

The delete method only needs one attribute to allow a correct deletion in PTV and that is the `reference` (if you are using friendly naming) or `IMPH_REFERENCE` (if you do not use friendly naming) attribute. And so when we want to delete an order we can do so like:

````php
...
use Noxxie\Ptv\Facades\Order;
...

$order = order::delete([
    'reference' => '12345678'
]);

$order->save();
````
This will add a delete record to the transfer database. The actual import in PTV will throw an error when the record cannot be deleted or cannot be found. There a various reasons why a order cannot be deleted within PTV.

# Saving the orders

After you created your creation, update or deletion of orders you must save those records to the database. You can do so by using the `save` method. This will insert all the records you have given to this package at once. This makes sure that all the records are not inserted one by one.

When you use the `save` method when there is nothing to save a `BadArgumentException` will be thrown.

# inserting creations, updates and deletions at once

The way this package is build you can function chain the `create`, `update`, `delete` and `save` methods. And so you can do everything at one to the database. The following examples shows the creation of a new order and the deletion of an existing order and saving it.

````php
...
use Noxxie\Ptv\Facades\Order;
...

$creationData = [
    'reference'     => '987654321',
    'priority'      => 1,
    'postcode'      => '3744AA',
    'city'          => 'Baarn',
    'street'        => 'Amsterdamsestraatweg',
    'houseno'       => '1',
    'country'       => 'NL',
    'timewindow'    => '501',
    'from'          => '20190101',
    'till'          => '20191231',
    'IORH_TEXT_1'   => 'Hello world',
];

$deletionData = [
    'reference' => '12345678'
];

$order = order::create($creationData)->delete($deletionData)->save();
````
