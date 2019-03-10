<?php

namespace Noxxie\Ptv\Contracts;

interface Order
{
    /**
     * Creates a new object for the order.
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function create(array $attributes);

    /**
     * Update an existing order.
     *
     * @param array $attributes
     *
     * @return void
     */
    public function update(array $attributes);

    /**
     * Delete an order.
     *
     * @param array $attributes
     *
     * @return void
     */
    public function delete(array $attributes);
}
