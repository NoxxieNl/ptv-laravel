<?php
namespace Noxxie\Ptv\Contracts;

interface Route {

    /**
     * Get specified route
     *
     * @param int $id
     * @param bool $latestOnly
     * @return Illuminate\Support\Collection
     */
    public function get(int $id, bool $latestOnly = true);

    /**
     * Retrieve routes that are not marked as imported
     *
     * @param string|null $type
     * @return Illuminate\Support\Collection
     */
    public function getNotImported(?string $type);

    /**
     * mark a route as the specified code
     *
     * @param \Noxxie\Ptv\Models\Route|Illuminate\Database\Eloquent\Collection $data
     * @param int $code
     * @return bool
     */
    public function markAs($data, int $code);
}
