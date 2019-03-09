<?php

namespace Noxxie\Ptv\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Noxxie\Ptv\Helpers\TableColumnDefinitions;
use Noxxie\Ptv\Helpers\UniqueIdGeneration;

trait defaultAttributes
{
    /**
     * Holds the unique ID that is needed for joining the import tables.
     *
     * @var int
     */
    protected $unique_id;

    /**
     * Set default attributes for each model using this trait.
     *
     * @return void
     */
    protected function fillWithDefaultAttributes()
    {
        $configData = config('ptv.defaults');
        if (count($configData) > 0) {
            foreach (config('ptv.defaults') as $table => $columns) {
                if (count($columns) > 0) {
                    foreach ($columns as $column => $data) {
                        if ($friendly = $this->isFriendlyAttribute($column)) {
                            $this->attributes[$friendly->get('table')][$friendly->get('column')] = $data;
                        } else {
                            $data = $this->fillPlaceHolderData($data);
                            $this->attributes[$table][$column] = $data;
                        }
                    }
                }
            }
        }
    }

    /**
     * Replace the place holder data with actuall data.
     *
     * @param string $data
     *
     * @return void
     */
    protected function fillPlaceHolderData(string $data)
    {
        // Replace the currentdate place holder with the actuall current date
        if ($data == '%CURRENT_DATE%') {
            return Carbon::now()->format('Ymd');
        }

        // Replace the unique ID place holder with an actuall unique ID, when non is generated yet generate one
        if ($data == '%UNIQUE_ID%') {
            if (!isset($this->unique_id)) {
                $this->unique_id = app()->make(UniqueIdGeneration::class)->generate();
            }

            return $this->unique_id;
        }

        // The unique IORA ID must be a string, therefor convert the integer to a string
        if ($data == '%UNIQUE_IORA_ID%') {
            return (string) $this->unique_id;
        }

        // Non replacement date found just return the original value
        return $data;
    }

    /**
     * Check if the given parameter is a column attribute.
     *
     * @param string $column
     *
     * @return bool
     */
    protected function isColumnAttribute(string $column)
    {
        if (!isset($this->columns)) {
            $this->fillColumnsVariable();
        }

        foreach ($this->columns as $table => $columns) {
            foreach ($columns as $columnName) {
                if ($column == $columnName) {
                    return new Collection([
                        'table'  => $table,
                        'column' => $columnName,
                    ]);
                }
            }
        }

        return false;
    }

    /**
     * Fill the column variable with all availible columns from every table needed.
     *
     * @return void
     */
    protected function fillColumnsVariable()
    {
        $this->columns = App()->make(TableColumnDefinitions::class)->all();
    }
}
