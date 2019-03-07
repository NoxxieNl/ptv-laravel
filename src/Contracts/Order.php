<?php

namespace Noxxie\Ptv\Contracts;

use Illuminate\Support\Collection;

interface Order
{
    /**
     * Creates a new object for the order.
     *
     * @param Illuminate\Support\Collection $attributes
     * @param bool $directSave
     *
     * @return mixed
     */
    public function create(Collection $attributes, bool $directSave);

    /**
     * Update an existing order.
     *
     * @param Illuminate\Support\Collection $attributes
     * @param bool $directSave
     *
     * @return void
     */
    public function update(Collection $attributes, bool $directSave);

    /**
     * Delete an order.
     *
     * @param Illuminate\Support\Collection $attributes
     * @param bool $directSave
     *
     * @return void
     */
    public function delete(Collection $attributes, bool $directSave);
}
