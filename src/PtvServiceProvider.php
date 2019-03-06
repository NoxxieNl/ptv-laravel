<?php

namespace Noxxie\Ptv;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use Noxxie\Ptv\Contracts\Order as OrderContract;
use Noxxie\Ptv\Contracts\Route as RouteContract;

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
        // Load in the config file for this packaage
        $this->mergeConfigFrom(
            __DIR__.'/../config/ptv.php',
            'ptv'
        );

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
