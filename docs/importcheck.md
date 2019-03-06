# ImportCheck

When it comes to check if PTV really imported the data there is no possible way to do it at the same time as when you create an order. Yeah sure the `order` instance returns exceptions when something goes wrong. But if the creation / update or deletion is excepted it is good right? PTV will always insert that record... right?

Well no... sometimes even when the transfer database excepts your newly created order it just sometimes fails. And of course we want to check if that happens.

Hence  the `ImportCheck` functionality. This object is written to be used in combination with the `task scheduling` option from Laravel in mind.

This object is a so called `invokable object` and such when invoked it will execute that method.

When this object is ran and it is executed the column `IMPH_DESCRIPTION` will be filled with the value `IMPORT_CHECK_EXECUTED` it will do this to all the records that have a matching value of `-30` or `50` within the column `IMPH_PROCESS_CODE`.

This object retrieves all the failed and succeeded imports from the transfer database that do not have a value within the column `IMPORT_CHECK_EXECUTED` and will evaluate them according to the callbacks.

## Callbacks

You can use this object by registering callbacks to it. With callbacks you can add extra code that will be executed. You can define to callback options `success` and `failed`. The most easy way is to register them in in your `providers\AppServiceProvider`  in the `register` method.

A example of registering a `success` and `failed` callback:
````php
<?php
...
use Noxxie\Ptv\ImportCheck;
...

public function register()
{
	...
	ImportCheck::registerCallback(function ($model) {
		echo  $model->IMPH_EXTID  . "\n";
	}, 'failed');

	  

	ImportCheck::registerCallback(function ($model) {
		echo $model->IMPH_EXTID . "\n";
	}, 'success');
}
````

The callbacks that you register needs to except one parameter. The parameter will be filled with a model instance of `Imph_import_header` and so you have full access to it within your call back.
