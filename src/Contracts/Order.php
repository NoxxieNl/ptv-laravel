<?php

namespace Noxxie\Ptv\Contracts;

use Illuminate\Support\Collection;

interface Order
{
    /**
     * Creates a new object for the order.
     *
     * @param Illuminate\Support\Collection $attributes
     *
     * @return mixed
     */
    public function create(Collection $attributes);

    /**
     * Update an existing order.
     *
     * @param Illuminate\Support\Collection $attributes
     *
     * @return void
     */
    public function update(Collection $attributes);

    /**
     * Delete an order.
     *
     * @param Illuminate\Support\Collection $attributes
     *
     * @return void
     */
    public function delete(Collection $attributes);
}
