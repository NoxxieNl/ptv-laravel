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

### Old fashion
Of course you can also use the old fashioned way and just create the class manually:
````php
<?php
...
use Noxxie\Ptv\Order;
...
public function mockup() {
    $Order = new Order();
}
````

## Methods

The order object has three methods you can use to control the data flow within PTV.

### Create(Collection $attributes, $directSave = true)

With the create method you can create a new record within PTV. It is important that an instance of the `\illuminate\support\collection` must be entered as the parameter of this method.

You can do so by using the `collect` function that is provided within the Laravel framework.

A simple example for creating a valid order:
````php
$data = collect([
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
]);
````

In combination with the configuration option friendly naming the above attributes are easy to read and to understand what data goes were. (See the configuration section to view this functionality).

When you did not specify a friendly naming option you an use the raw column name to add data to the order. In the above example `IORH_TEXT_1` does not have a friendly_name configured and so the raw column name is used to defined the import data.

**Caution** the create order does not check if the order exists within PTV. If the order with the same `reference` already exists within PTV it will **UPDATE** the order instead of creating a new one. This is the way the import in PTV works.

### Update(Collection $attributes, $directSave = true)

With the update method you can update a existing order within PTV. It is important that an instance of the `\illuminate\support\collection` must be entered as the parameter of this method.

You can do so by using the `collect` function that is provided within the Laravel framework.

The implementation of the methods `update` and `create` are exactly the same the only difference is that the column name `IMPH_ACTION_CODE` is set to `UPDATE` instead of `NEW`.

**Caution** the update order does not check if the order exists within PTV. If the order does **NOT** exist within PTV it will create a new order instead. This is the way PTV works.

### Delete(Collection $attributes, $directSave = true)

When you want to delete an existing order within PTV you can use the Delete method. It is important that an instance of the `\illuminate\support\collection` must be entered as the parameter of this method.

The delete method only needs one attribute to allow a correct deletion in PTV and that is the `reference` attribute. And so when we want to delete an order we can do so like:

````php
$order = App()->MakeWith('Noxxie\Ptv\Order', [
    'type' => 'DELETE',
    'attributes' => collect([
        'reference' => 12345678,
    ])
]);
````
This will add a delete record to the transfer database. The actual import in PTV will throw an error when the record cannot be deleted or cannot be found. There a various reasons why a order cannot be deleted within PTV.

## The directSave functionality
As you may have noticed every method within this class has a `$directSave` parameter. When this parameter is set to `false` the data added to the model will only be validated. (And when not valid it will throw an exception).

This is so you can insert allot of records at once with speed. When the `$directSave` is active every order you want to insert will be inserted one by one.

With the `$directSave` option set to `false` you need to make one more call after you created every `order` instance you wanted.

And that is the `massSave()` method. This is a `static` method within `order` class itself to allow the mass save of every created `order` record you have created.

````php
    order::massSave(collection $collection);
````

The only parameter that needs to be enterd is a valid `collection` of all the orders you want to insert into the database and such you need to "collect" every order instance you created. For example when you want to delete two orders at once:

````php
$data = collect([
    'reference'     => '9876543212'
]);

$data2 = collect([
    'reference'     => '1234567892'
]);

$order = App()->make(Order::class);
$order->delete($data, false);

$order2 = App()->make(Order::class);
$order2->delete($data2, false);

$order::massSave(collect([$order, $order2]));
````

Because of how the `massSave` method works you can call this method from any of your created `order` instances. The method will add all the specified `order` records into the PTV transfer database and after they are inserted update them all at once to code `20` to let PTV know those rows can be imported.
