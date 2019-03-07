# ImportCheck

When it comes to check if PTV really imported the data there is no possible way to do it at the same time as when you create an order. Yeah sure the `order` instance returns exceptions when something goes wrong. But if the creation / update or deletion is excepted it is good right? PTV will always insert that record... right?

Well no... sometimes even when the transfer database excepts your newly created order it just sometimes fails. And of course we want to check if that happens.

Hence  the `ImportCheck` functionality. This object is written to be used in combination with the `task scheduling` option from Laravel in mind.

This object is a so called `invokable object` and such when invoked it will execute that method.

When this object is ran and it is executed the column `IMPH_DESCRIPTION` will be filled with the value `IMPORT_CHECK_EXECUTED` it will do this to all the records that have a matching value of `-30` or `50` within the column `IMPH_PROCESS_CODE`.

This object retrieves all the failed and succeeded imports from the transfer database that do not have a value within the column `IMPORT_CHECK_EXECUTED` and will evaluate them according to the callbacks.

## Installation

To allow the automatic run of importcheck you need to define a schedule option in your `App\Console\Kernel.php` file. Within the `schedule` method you can add the importcheck functionality and define in what schedule you must run it.

For example:
````php
...
use Noxxie\Ptv\ImportCheck;
..

protected function schedule(Schedule $schedule)
{
    ...
    $schedule->call(new ImportCheck)->hourly();
}
````
With the above example the import check will be ran every hour. Please note that the automating option works best if the import functionality in PTV itself is also set to automaticly import the data.

For more information regarding the scheduling option please review the Laravel documention about the `scheduling` option.

## Configuration

There is one availible option you can configure for this invokable oject. That is the `useupdateimportcallbacks` configuration option. The default value for this option is `true` and what this does is it automaticly registers two callbacks that are written in the `PtvServiceProvider`.

These two callbacks update the `imph_import_header` and to be more specific the column `IMPH_DESCRIPTION`. When he importcheck has been ran these callbacks will be executed and every line in that specific exection will be marked as checked.

When you set this configuration option to `false` please be aware that that importcheck object does not have any reference to check if the rows in the `imph_import_header` are checked and on every run every row will be checked again.

## Registering a callback

You can use this object by registering callbacks to it. With callbacks you can add extra code that will be executed. You can define to callback options `success` and `failed`. The most easy way is to register them in in your `providers\AppServiceProvider`  in the `register` method.

There are two ways you can defined callbacks, so called local callbacks that are called for every model separately or a global callbacks that are called once and pushed a `eloquent collection` to your defined callback.

The idea behind local and global callbacks is when you want to output something to your screen (perhaps when you want to run it in CLI) you can define a local callback. But when you want to update your database records (like the default callbacks within this package) a global callback is more suitable because you can update every record at once instead of every record separately. 

### Local callback

A example of registering a `success` and `failed` local callback:
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

The callbacks that you register needs to except one parameter. The parameter will be filled with a model instance of `Imph_import_header` and so you have full access to it within your callback.

### Global callback

A example of registering a `failed` and `success` global callback:
````php
<?php
...
use Noxxie\Ptv\ImportCheck;
...

public function register()
{
    ...
    ImportCheck::registerCallback(function ($models) {
        foreach ($models as $model)
        {
            echo  $model->IMPH_EXTID  . "\n";
        }
    }, 'failed', true);

	  

    ImportCheck::registerCallback(function ($models) {
        foreach ($models as $model)
        {
            echo  $model->IMPH_EXTID  . "\n";
        }
    }, 'success', true);
}
````
The callbacks that you register needs to except one parameter. The parameter will be filled with a instance of `Illuminate\Database\Eloquent\Collection` and so you have full access to it within your call back. Even when there is only one record send to your callback it still will be in a `collection`.

You can also check the [PtvServiceProvider](https://github.com/NoxxieNl/ptv-laravel/blob/master/src/PtvServiceProvider.php#L45) file how the default callbacks are registerd within the package. 
