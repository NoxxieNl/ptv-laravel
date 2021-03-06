<?php

namespace Noxxie\ptv\models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Etpt_tour_header extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ETPT_TOUR_HEADER';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'ETPT_EXPH_REFERENCE';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'float';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['ETPT_EXTID1', 'ETPT_START_DATETIME', 'ETPT_END_DATETIME', 'ETPT_LATEST_TOUR_START', 'ETPT_START_OF_DEPOT_AVAIL', 'ETPT_END_OF_DEPOT_AVAIL', 'ETPT_PRECOMBINED_TOUR_ID', 'ETPT_CUSTOMER_TOUR_EXTID', 'ETPT_RETURN_STATE', 'ETPT_ORDER_COUNT', 'ETPT_TOURPOINT_COUNT', 'ETPT_NOTE', 'ETPT_LOCATION_FROM_TO_CITY', 'ETPT_VEHICLE_EXTID1', 'ETPT_VEHICLE_LICENSE_PLATE', 'ETPT_HAULIER_EXTID1', 'ETPT_CODRIVER_NEEDED', 'ETPT_EARLINESS', 'ETPT_TOTAL_DISTANCE', 'ETPT_DRIVE_DISTANCE', 'ETPT_EMPTY_DISTANCE', 'ETPT_ROUTE_DISTANCE', 'ETPT_CONTINUATION_DISTANCE', 'ETPT_TOTAL_DISTANCENORETDEPOT', 'ETPT_TOTAL_DURATION', 'ETPT_DRIVE_DURATION', 'ETPT_EMPTY_DURATION', 'ETPT_ROUTE_DURATION', 'ETPT_CONTINUATION_DURATION', 'ETPT_TOTAL_DURATIONNORETDEPOT', 'ETPT_TOTAL_DRIVING_TIME', 'ETPT_DRIVING_TIME_FACTOR', 'ETPT_TOTAL_HANDLING_TIME', 'ETPT_TOTAL_IDLE_TIME', 'ETPT_TOTAL_LOADING_TIME', 'ETPT_TOTAL_UNLOADING_TIME', 'ETPT_TOTAL_BREAK_TIME', 'ETPT_TOTAL_REST_TIME', 'ETPT_TOTAL_TURNAROUND_TIME', 'ETPT_TOUR_RESTPERIOD', 'ETPT_WAITING_PERIOD', 'ETPT_SHIFTPOTENTIALSECS', 'ETPT_MERGED_VIOLATIONS', 'ETPT_WEIGHT', 'ETPT_VOLUME', 'ETPT_LOADMETER', 'ETPT_QUANTITY_1', 'ETPT_QUANTITY_2', 'ETPT_QUANTITY_3', 'ETPT_QUANTITY_4', 'ETPT_QUANTITY_5', 'ETPT_QUANTITY_6', 'ETPT_QUANTITY_7', 'ETPT_REST_CAPACITY_WEIGHT', 'ETPT_REST_CAPACITY_VOLUME', 'ETPT_REST_CAPACITY_LOADMETER', 'ETPT_REST_CAPACITY_1', 'ETPT_REST_CAPACITY_2', 'ETPT_REST_CAPACITY_3', 'ETPT_REST_CAPACITY_4', 'ETPT_REST_CAPACITY_5', 'ETPT_REST_CAPACITY_6', 'ETPT_REST_CAPACITY_7', 'ETPT_SUM_WEIGHT', 'ETPT_SUM_VOLUME', 'ETPT_SUM_LOADMETER', 'ETPT_SUM_QUANTITY_1', 'ETPT_SUM_QUANTITY_2', 'ETPT_SUM_QUANTITY_3', 'ETPT_SUM_QUANTITY_4', 'ETPT_SUM_QUANTITY_5', 'ETPT_SUM_QUANTITY_6', 'ETPT_SUM_QUANTITY_7', 'ETPT_PICKUP_WEIGHT', 'ETPT_PICKUP_VOLUME', 'ETPT_PICKUP_LOADMETER', 'ETPT_PICKUP_QUANTITY1', 'ETPT_PICKUP_QUANTITY2', 'ETPT_PICKUP_QUANTITY3', 'ETPT_PICKUP_QUANTITY4', 'ETPT_PICKUP_QUANTITY5', 'ETPT_PICKUP_QUANTITY6', 'ETPT_PICKUP_QUANTITY7', 'ETPT_DELIVERY_WEIGHT', 'ETPT_DELIVERY_VOLUME', 'ETPT_DELIVERY_LOADMETER', 'ETPT_DELIVERY_QUANTITY1', 'ETPT_DELIVERY_QUANTITY2', 'ETPT_DELIVERY_QUANTITY3', 'ETPT_DELIVERY_QUANTITY4', 'ETPT_DELIVERY_QUANTITY5', 'ETPT_DELIVERY_QUANTITY6', 'ETPT_DELIVERY_QUANTITY7', 'ETPT_PERCENT_WEIGHT', 'ETPT_PERCENT_VOLUME', 'ETPT_PERCENT_LOADMETER', 'ETPT_PERCENT_QUANTITY1', 'ETPT_PERCENT_QUANTITY2', 'ETPT_PERCENT_QUANTITY3', 'ETPT_PERCENT_QUANTITY4', 'ETPT_PERCENT_QUANTITY5', 'ETPT_PERCENT_QUANTITY6', 'ETPT_PERCENT_QUANTITY7', 'ETPT_MINHEIGHT', 'ETPT_MAXHEIGHT', 'ETPT_MINLENGTH', 'ETPT_MAXLENGTH', 'ETPT_MINWIDTH', 'ETPT_MAXWIDTH', 'ETPT_TASKFIELDNAMES', 'ETPT_TOTAL_TOUR_COSTS', 'ETPT_COST_TOTAL_NORETDEPOT', 'ETPT_TOTAL_TOLL_COSTS', 'ETPT_TOTAL_TOLL_DISTANCE', 'ETPT_TOLL_COSTS_NORETDEPOT', 'ETPT_TOLL_DISTANCENORETDEPOT', 'ETPT_TOTAL_FREIGHT_COSTS', 'ETPT_FREIGHT_COSTS_MANUAL', 'ETPT_FREIGHT_COSTS_CALCULATED', 'ETPT_ECOTAXE', 'ETPT_ECOTAXE_DISTANCE', 'ETPT_EXECUTION_STATE', 'ETPT_EXECUTION_STATETIME', 'ETPT_EXECUTION_TOURSTART', 'ETPT_EXECUTION_TOUREND', 'ETPT_EXECUTION_TOURDELAY', 'ETPT_TELEMATICS_STATE', 'ETPT_ISINEXECUTION', 'ETPT_NUM_1', 'ETPT_NUM_2', 'ETPT_NUM_3', 'ETPT_NUM_4', 'ETPT_NUM_5', 'ETPT_NUM_6', 'ETPT_NUM_7', 'ETPT_NUM_8', 'ETPT_NUM_9', 'ETPT_NUM_10', 'ETPT_TEXT_1', 'ETPT_TEXT_2', 'ETPT_TEXT_3', 'ETPT_TEXT_4', 'ETPT_TEXT_5', 'ETPT_TEXT_6', 'ETPT_TEXT_7', 'ETPT_TEXT_8', 'ETPT_TEXT_9', 'ETPT_TEXT_10', 'ETPT_CREATE_USER', 'ETPT_SCEMID'];

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function exphexportheader()
    {
        return $this->belongsTo(Exph_export_header::class, 'ETPT_EXPH_REFERENCE', 'EXPH_REFERENCE');
    }

    /**
     * Create Carbon object voor ETPT_START_DATETIME.
     *
     * @param string $value
     *
     * @return Carbon\Carbon
     */
    public function getEtptStartDatetimeAttribute(string $value)
    {
        $hackDts = explode('+', $value);

        return Carbon::createFromFormat('Y-m-d\TH:i:s', $hackDts[0]);
    }

    /**
     * Create Carbon object voor ETPT_END_DATETIME.
     *
     * @param string $value
     *
     * @return Carbon\Carbon
     */
    public function getEtptEndDatetimeAttribute(string $value)
    {
        $hackDts = explode('+', $value);

        return Carbon::createFromFormat('Y-m-d\TH:i:s', $hackDts[0]);
    }
}
