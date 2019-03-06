# Route

With the route instance you can retrieve an exported route from PTV. This is useful if you want to update your application database with the defined data from PTV.

## Initiate an order object

You can initiate a route object in different ways

### Dependency injection
You can access one by dependency injection provided by Laravel:

````php
<?php
...
use Noxxie\Ptv\Route;
...

public function mockup(Route $route) {
	$route->create(...);
}
````
When you use the dependency injection option you will have an instance of the `route` object. And so you can directly use the functionality provided by that class.

### Resolving it from the service container
You can resolve the instances from the service container using:
````php
$Route = App()->Make('Noxxie\Ptv\Route');
````

When you want to retrieve a route straight away from the service container you can do so as follow:

````php
$route = App()->MakeWith('Noxxie\Ptv\Route', [
	'id' => 1234
]);
````

### Old fashion
Of course you can also use the old fashioned way and just create the class manually:
````php
<?php
...
use Noxxie\Ptv\Route;
...
public function mockup() {
	$route = new Route();
}
````

## Methods

The route object has serveral methods to retrieve data from the transfer database to make it easier for you to read the data.

### get($id, $latestOnly = true)

With the get option you can retrieve a specific route from the transfer database. This option does not take the "already imported" option into account. When the parameter `$latestOnly` is not specified or defined as `true` it will return the latest exported variant of the specified route.

When the parameter is set to `false` every export record for that route will be returned regardless if it is an `UPDATE` or `DELETE`.

When the specified route is not found a `null` value will be returned. When the `$latestOnly` parameter is `true` an instance of `Noxxie\Ptv\Models\Route` will be returned.

When the `$latestOnly` parameter is set to `false` a instance of `Illuminate\Database\Eloquent\Collection` will be returned regardless if there was only one record or multiple records.

### getNotImported($status = null)

With this method you can retrieve any record that has not been exported in your own application or is not marked as imported. The valid status types are `NEW`, `UPDATE`, `DELETE`. Any other status will  throw an exception.

When no records are found a `null` value will be returned.

When the are results a instance of `Illuminate\Database\Eloquent\Collection` will be returned regardless if there is one route to be exported or multiple.

When the `$status` option is specified the the returning value will only contain the export routes for that specific status.

### markAs(Noxxie\Ptv\Models\Route $data, $code)

The markAs method gives you the option the update a exported route to a new value. The `$data` parameter must contain a valid instance of the `Noxxie\Ptv\Models\Route` object. The `$code` parameter must have one of the following values (20, 30, 50 -30). (Read the PTV documentation for further explanation about the different codes).

### markAsImported(Noxxie\Ptv\Models\Route $data)

Extra helper method to make it easier to update a record as imported. This method just calls the `markAs` method with `$code` specified with `50`.

### markAsFailed(Noxxie\Ptv\Models\Route $data)

Extra helper method to make it easier to update a record as imported. This method just calls the `markAs` method with `$code` specified with `-30`.

## The Noxxie\Ptv\Models\Route model

The `Noxxie\Ptv\Models\Route` leverages the default `eloquent` models within Laravel.  And so every option that is available within a `eloquent` model is also available in this model.

However, this model is constructed manually and the relationships are also added manually to this model. This ensures a quick experience for the user and easy programming. Because the PTV transfer database uses composite keys to define indexes I needed a way to make things a bit easier.

The `route` model has the attributes that are the same as all the columns for the `exph_export_header` model (or table). The only difference is the attributes are all lower cased and the prefix `exph_` has been removed from the attribute names.

The `route` model has one relation and that is with the `routeDetail` model and is added upon creation the `route` model.

The `routeDetail` model consits of all the data that is stored within the `Etpa_tour_actionpoint` model (or table). The only difference is the attributes are all lower cased and the prefix `etpa_` has been removed from the attribute names.

Because a relation is created between the `route` and the `routeDetail` models. You can access the `routeDetail` model from the `route` model using 
````php 
$route->routeDetails;
````
