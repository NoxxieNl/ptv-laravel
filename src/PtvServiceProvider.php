<?php
namespace Noxxie\Ptv;

use Illuminate\Support\ServiceProvider;

class PtvServiceProvider extends ServiceProvider 
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
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
        $this->mergeConfigFrom(
            __DIR__.'/../config/ptv.php',
            'ptv'
        );

        $this->app->bind(AddOrder::class, function () {
            return new AddOrder();
        });

        $this->app->bind(DeleteOrder::class, function () {
            return new DeleteOrder();
        });

        $this->app->bind(UpdateOrder::class, function () {
            return new UpdateOrder();
        });

        $this->app->bind(GetRoute::class, function () {
            return new GetRoute();
        });
    }
}