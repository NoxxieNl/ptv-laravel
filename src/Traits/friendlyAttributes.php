<?php

namespace Noxxie\Ptv\Traits;

use Illuminate\Support\Collection;

trait friendlyAttributes
{
    /**
     * Check if the given parameter is a friendly attribute if so return a collection holding the correct table and column name.
     *
     * @param string $column
     *
     * @return bool
     */
    public function isFriendlyAttribute(string $column)
    {
        $configData = config('ptv.friendly_naming');

        if (!is_null($configData) and count($configData) > 0) {
            foreach ($configData as $table => $columns) {
                if (count($columns) > 0) {
                    foreach ($columns as $friendlyColumnName => $columnName) {
                        if ($friendlyColumnName == $column) {
                            return new Collection([
                                'table'  => $table,
                                'column' => $columnName,
                            ]);
                        }
                    }
                }
            }
        }

        return false;
    }
}
