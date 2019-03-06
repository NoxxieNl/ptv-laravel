<?php

namespace Noxxie\ptv\models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Etpa_tour_actionpoint extends Model
{
    use Compoships;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ETPA_TOUR_ACTIONPOINT';

    /**
     * @var array
     */
    protected $fillable = ['ETPA_POS_IN_TOUR', 'ETPA_ORDER_EXTID1', 'ETPA_ARRIVAL_TIME', 'ETPA_START_SERVICE_TIME', 'ETPA_END_SERVICE_TIME', 'ETPA_OH_TOLERANCE', 'ETPA_DELAY', 'ETPA_DEPARTURE_TIME', 'ETPA_EARLINESS', 'ETPA_IDLE_PERIOD', 'ETPA_SERVICE_PERIOD', 'ETPA_WAITING_PERIOD', 'ETPA_ACTION', 'ETPA_STARTING_TIME', 'ETPA_ENDING_TIME', 'ETPA_LOCATION_EXTID1', 'ETPA_LOCATION_EXTID2', 'ETPA_LOCATION_NAME', 'ETPA_COUNTRY', 'ETPA_STATE', 'ETPA_POSTCODE', 'ETPA_CITY', 'ETPA_DISTRICT', 'ETPA_STREET', 'ETPA_HOUSENO', 'ETPA_LATITUDE', 'ETPA_LONGITUDE', 'ETPA_COORD_TYPE', 'ETPA_GEOCODING_CLASSIFICATION', 'ETPA_WEIGHT', 'ETPA_VOLUME', 'ETPA_LOADMETER', 'ETPA_QUANTITY_1', 'ETPA_QUANTITY_2', 'ETPA_QUANTITY_3', 'ETPA_QUANTITY_4', 'ETPA_QUANTITY_5', 'ETPA_QUANTITY_6', 'ETPA_QUANTITY_7', 'ETPA_SPLIT_ACTION', 'ETPA_SPLIT_SEQUENCE', 'ETPA_SPLIT_ASSORT_GROUP_EXTID', 'ETPA_SPLIT_BASE_ORDER_EXTID', 'ETPA_SPLIT_INFO', 'ETPA_SPLIT_RESULT', 'ETPA_SPLIT_ROLE', 'ETPA_NUMBER_OF_SPLITS', 'ETPA_GROUPED_ORDER_EXTID', 'ETPA_GROUPED_ORDER_ROLE', 'ETPA_GROUPED_ORDER_PART_COUNT', 'ETPA_GROUPED_ORDER_SEQUENCE', 'ETPA_NUM_1', 'ETPA_NUM_2', 'ETPA_NUM_3', 'ETPA_NUM_4', 'ETPA_NUM_5', 'ETPA_NUM_6', 'ETPA_NUM_7', 'ETPA_NUM_8', 'ETPA_NUM_9', 'ETPA_NUM_10', 'ETPA_TEXT_1', 'ETPA_TEXT_2', 'ETPA_TEXT_3', 'ETPA_TEXT_4', 'ETPA_TEXT_5', 'ETPA_TEXT_6', 'ETPA_TEXT_7', 'ETPA_TEXT_8', 'ETPA_TEXT_9', 'ETPA_TEXT_10', 'ETPA_STABLE_SEQ_NUMBER', 'ETPA_OLD_STABLE_SEQ_NUMBER', 'ETPA_PROVINCE', 'ETPA_LOCATION_FUNCTION'];

    /**
     * Indicates if timestamp columns are present in the table.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection;

    /**
     * Constructor function.
     */
    public function __construct()
    {
        // Correct the connection setting from config
        $this->connection = config('ptv.connection');

        // Allow parent to do his work
        parent::__construct();
    }

    /**
     * Scope a query to only include non depot locations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutDepot(Builder $query)
    {
        return $query->where('ETPA_LOCATION_FUNCTION', '!=', 'DEPOT');
    }
}
