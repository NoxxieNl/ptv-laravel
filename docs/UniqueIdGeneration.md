# UniqueIdGeneration

When you want to import data to the PTV transfer databse you must specify a unique ID for each new import. However sometimes it can happen you must import the same order from your own application to the PTV database. Then the unique ID of your own application suddenly is not unique anymore. Or perhaps you want to send an update to PTV for a specific order... well thats where this helper class comes in.

Binded as a `singleton` in the service container this class takes care of generation a unique random ID for your order creating / updateing or deleting insertions in the database. If you use the default configuration of this package the placeholder from the config `%UNIQUE_ID%` uses this class.

## Usage

First of all you must resolve it from the container:
````php
$idGenerator = App()->Make('Noxxie\Ptv\helpers\UniqueIdGeneration');
````

Because how this packages registers the class within the container you can only resolve the concrete class of `UniqueIdGeneration` once. When you do another call to the service contrainer and retry to resolve the `UniqueIdGeneration` the container will return the first initiated version. Hence we use the `singleton` pattern here.

After you resolved it from the container you can either generate a new ID, manually add a new ID to the `already used ID's stack` or delete an existing ID from the `already used ID's stack`.

To generate an ID you can simply use:
````php
$idGenerator->generate();
````

Or even shorter (one liner):
````php
$id = App()->Make('Noxxie\Ptv\helpers\UniqueIdGeneration')->generate();
````

You can specify how many `retries` must be done to get an unique ID before the helper throws an `RunTimeException` exception.

To manually add an ID to the `stack` you can use the `add` method:
````php
$idGenerator->add(123);
````

And to remove one you can use the `remove` method:
````php
$idGenerator->remove(123);
````
