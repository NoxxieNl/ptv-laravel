<?php

namespace Noxxie\ptv\models;

use Illuminate\Database\Eloquent\Model;

use Noxxie\Ptv\Models\Exph_export_header;
use Noxxie\Ptv\Models\Etpa_tour_actionpoint;

use \Awobaz\Compoships\Compoships;

class Etps_tour_stop extends Model
{
    use Compoships;

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'ETPS_TOUR_STOP';

    /**
     * @var array
     */
    protected $fillable = ['ETPS_ARRIVAL_TIME', 'ETPS_DEPARTURE_TIME', 'ETPS_START_SERVICE_TIME', 'ETPS_END_SERVICE_TIME', 'ETPS_DELAY', 'ETPS_EARLINESS', 'ETPS_BREAK_PERIOD_ON_ROAD', 'ETPS_BREAK_PERIOD_AT_STOP', 'ETPS_REST_PERIOD_ON_ROAD', 'ETPS_REST_PERIOD_AT_STOP', 'ETPS_DRIVING_PERIOD', 'ETPS_SERVICE_PERIOD', 'ETPS_WAITING_PERIOD', 'ETPS_TOURPOINT_COSTS', 'ETPS_TOTAL_TOLL_COSTS', 'ETPS_TOTAL_TOLL_DISTANCE', 'ETPS_ECOTAXE', 'ETPS_ECOTAXE_DISTANCE', 'ETPS_ROUTE_DISTANCE', 'ETPS_ROUTE_DURATION', 'ETPS_START_DISTANCE', 'ETPS_PRED_DISTANCE', 'ETPS_CURRENT_WEIGHT', 'ETPS_CURRENT_VOLUME', 'ETPS_CURRENT_LOADMETER', 'ETPS_CURRENT_QUANTITY_1', 'ETPS_CURRENT_QUANTITY_2', 'ETPS_CURRENT_QUANTITY_3', 'ETPS_CURRENT_QUANTITY_4', 'ETPS_CURRENT_QUANTITY_5', 'ETPS_CURRENT_QUANTITY_6', 'ETPS_CURRENT_QUANTITY_7', 'ETPS_NUM_1', 'ETPS_NUM_2', 'ETPS_NUM_3', 'ETPS_NUM_4', 'ETPS_NUM_5', 'ETPS_NUM_6', 'ETPS_NUM_7', 'ETPS_NUM_8', 'ETPS_NUM_9', 'ETPS_NUM_10', 'ETPS_TEXT_1', 'ETPS_TEXT_2', 'ETPS_TEXT_3', 'ETPS_TEXT_4', 'ETPS_TEXT_5', 'ETPS_TEXT_6', 'ETPS_TEXT_7', 'ETPS_TEXT_8', 'ETPS_TEXT_9', 'ETPS_TEXT_10', 'ETPS_COUNTRY', 'ETPS_STATE', 'ETPS_POSTCODE', 'ETPS_CITY', 'ETPS_DISTRICT', 'ETPS_STREET', 'ETPS_HOUSENO', 'ETPS_LATITUDE', 'ETPS_LONGITUDE', 'ETPS_COORD_TYPE', 'ETPS_EXEC_SEQUENCE', 'ETPS_EXEC_TOURPOINT_STATE', 'ETPS_EXEC_TOURPOINT_STATE_TIME', 'ETPS_EXEC_ARRIVAL_TIME', 'ETPS_EXEC_DEPARTURE_TIME', 'ETPS_EXEC_ARRIVAL_TIME_OBU', 'ETPS_EXEC_DEPARTURE_TIME_OBU', 'ETPS_EXEC_TOURSTOP_DELAY', 'ETPS_PROVINCE', 'ETPS_SCEMID'];

    /**
     * Indicates if timestamp columns are present in the table
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection;

    /**
     * Constructor function
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
        return $this->belongsTo(Exph_export_header::class, 'ETPS_EXPH_REFERENCE', 'EXPH_REFERENCE');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function etpatouractionpoint()
    {
        return $this->hasMany(Etpa_tour_actionpoint::class,
            [
                'ETPA_ETPS_EXPH_REFERENCE', 
                'ETPA_ETPS_TOURPOINT_SEQUENCE'
            ],
            [
                'ETPS_EXPH_REFERENCE', 
                'ETPS_TOURPOINT_SEQUENCE'
            ]
            );
    }
}
