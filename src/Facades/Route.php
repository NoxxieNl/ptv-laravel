<?php
namespace Noxxie\Ptv\Facades;

use Illuminate\Support\Facades\Facade;

class Route extends Facade
{

    /**
     * @see \Noxxie\Ptv\Contracts\Route
     */
    protected static function getFacadeAccessor() {
        return 'route';
    }
}
