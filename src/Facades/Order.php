<?php
namespace Noxxie\Ptv\Facades;

use Illuminate\Support\Facades\Facade;

class Order extends Facade
{

    /**
     * @see \Noxxie\Ptv\Contracts\Order
     */
    protected static function getFacadeAccessor() {
        return 'order';
    }
}
