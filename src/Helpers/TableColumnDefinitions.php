<?php
namespace Noxxie\Ptv\Helpers;

use Noxxie\Ptv\Models\Imph_import_header;
use Noxxie\Ptv\Models\Iorh_order_header;
use Illuminate\Support\Facades\Schema;

class TableColumnDefinitions {

    /**
     * Holds all the definitions of the fetched columns
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Create the column definitions of the tables we need
     */
    public function __construct()
    {
        $tableName = (new Imph_import_header())->getTable();
        $this->columns[$tableName] = Schema::connection(config('ptv.connection'))->getColumnListing($tableName);

        $tableName = (new Iorh_order_header())->getTable();
        $this->columns[$tableName] = Schema::connection(config('ptv.connection'))->getColumnListing($tableName);

        $tableName = (new Iora_order_actionpoint())->getTable();
        $this->columns[$tableName] = Schema::connection(config('ptv.connection'))->getColumnListing($tableName);
    }

    /**
     * Get all the columns of the specified column
     *
     * @param string $tableName
     *
     * @return void
     */
    public function get(string $tableName)
    {
        if (!array_key_exists($tableName, $this->columns)) {
            throw new InvalidArgumentException(sprintf('The columns for table "%s" could be fetches', $tableName));
        }

        return $this->columns[$tableName];
    }

    /**
     * Return the complete array of defined columns
     *
     * @return void
     */
    public function all()
    {
        return $this->columns;
    }
}
