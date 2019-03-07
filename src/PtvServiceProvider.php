<?php

namespace Noxxie\Ptv;

use Noxxie\Ptv\Contracts\Order as OrderContract;
use Noxxie\Ptv\Contracts\Route as RouteContract;
use Noxxie\Ptv\ImportCheck;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class PtvServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Allow the publication of the ptv config file
        $this->publishes([
            __DIR__.'/../config/ptv.php' => config_path('ptv.php'),
        ], 'config');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        // Load in the config file for this package
        $this->mergeConfigFrom(
            __DIR__.'/../config/ptv.php',
            'ptv'
        );

        $this->registerBindings();
        $this->registerDefaultCallbacks();
    }

    protected function registerDefaultCallbacks()
    {
        // Only add the callbacks if the config value is set to true, if no configuration value is found we also execute it
        if (config('ptv.useupdateimportcallbacks', true)) {
            ImportCheck::registerCallback(function ($models) {
                $failedReferences = [];

                // Loop every model and fetch the reference
                foreach ($models as $model)
                {
                    $failedReferences[] = $model->IMPH_REFERENCE;
                }

                // Run the update statement
                DB::connection(config('ptv.connection'))
                    ->table($models[0]->getTable())
                    ->whereIn('IMPH_REFERENCE', $failedReferences)
                    ->update(['IMPH_DESCRIPTION' => 'IMPORT_CHECK_EXECUTED']);
            }, 'failed', true);

            ImportCheck::registerCallback(function ($models) {
                $successReferences = [];

                // Loop every model and fetch the reference
                foreach ($models as $model)
                {
                    $successReferences[] = $model->IMPH_REFERENCE;
                }

                // Run the update statement
                DB::connection(config('ptv.connection'))->table($models[0]
                    ->getTable())
                    ->whereIn('IMPH_REFERENCE', $successReferences)
                    ->update(['IMPH_DESCRIPTION' => 'IMPORT_CHECK_EXECUTED']);
            }, 'success', true);
        }
    }

    protected function registerBindings()
    {
        /*
            Bind the route contract into the container, it can be resolved by independency injection
            It can also be resolved from the service container to execute a method when the class is created
        */
        $this->app->bind(
            OrderContract::class,
            function ($app, array $parameters) {
                if (count($parameters) == 0) {
                    return new Order();
                } else {
                    if (!isset($parameters['attributes']) or !isset($parameters['type']) or !$parameters['attributes'] instanceof Collection) {
                        throw new InvalidArgumentException('Missing required parameters');
                    }

                    // The constructor function defines a outcome variable so we can check if the execution of a method succeeded
                    $order = new Order($parameters['type'], $parameters['attributes']);

                    return $order->outcome;
                }
            }
        );

        /*
            Bind the route contract into the container, it can be resolved by independency injection
            and it can also be resolved from the service container with an route id specified
        */
        $this->app->bind(
            RouteContract::class,
            function ($app, array $parameters) {
                if (count($parameters) == 0) {
                    return new Route();
                } else {
                    if (!isset($parameters['id'])) {
                        throw new InvalidArgumentException('Missing required parameters');
                    }

                    $class = new Route();

                    return $class->get($parameters['id']);
                }
            }
        );
    }
}
