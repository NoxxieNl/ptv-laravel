<?php
namespace Noxxie\Ptv;

use Noxxie\Ptv\Addorder;
use Illuminate\Support\collection;

class UpdateOrder extends AddOrder
{
    public function create(collection $collection)
    {
        // Overwrite IMPH_ACTION_CODE to update
        $collection->put('IMPH_ACTION_CODE', 'UPDATE');

        return parent::create($collection);
    }
}