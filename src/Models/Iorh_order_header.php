<?php

namespace Noxxie\Ptv\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Validating\ValidatingTrait;

class Iorh_order_header extends Model
{
    use ValidatingTrait;

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

        // Correct the rules settings
        $this->rules['IORH_IMPH_REFERENCE'] = str_replace('ptv.', config('ptv.connection').'.', $this->rules['IORH_IMPH_REFERENCE']);

        // Allow parent to do his work
        parent::__construct();
    }

    /**
     * Validation rules for validation the specified information
     * if any of the data is not valid the model will not insert the record.
     *
     * @var array
     */
    protected $rules = [
        'IORH_IMPH_REFERENCE'           => 'required|numeric|digits_between:0,999999999',
        'IORH_ORDER_TYPE'               => 'required|string|max:20|in:TRANSPORT,DELIVERY,PICKUP',
        'IORH_EXTID2'                   => 'string|max:50',
        'IORH_CUSTOMER_ORDER_EXTID'     => 'string|max:50',
        'IORH_NOTE'                     => 'string|max:255',
        'IORH_ASSORTMENT'               => 'string|max:50',
        'IORH_CUSTOMER'                 => 'string|max:50',
        'IORH_CONSIGNMENT_EXTID'        => 'string|max:50',
        'IORH_LOGISTIC_EXTID'           => 'string|max:50',
        'IORH_PRECOMBINED_TOUR'         => 'string|max:255',
        'IORH_PREASSIGNED_TRUCK'        => 'string|max:50',
        'IORH_VEHICLEREQUIREMENTS'      => 'string|max:255',
        'IORH_PRIORITY'                 => 'integer|between:0,9',
        'IORH_CODRIVER_NEEDED'          => 'required|in:0,1',
        'IORH_TRANSPORTPERIOD'          => 'integer',
        'IORH_SOLO'                     => 'required|in:0,1',
        'IORH_WEIGHT'                   => 'numeric',
        'IORH_VOLUME'                   => 'numeric',
        'IORH_LOADMETER'                => 'numeric',
        'IORH_QUANTITY_1'               => 'numeric',
        'IORH_QUANTITY_2'               => 'numeric',
        'IORH_QUANTITY_3'               => 'numeric',
        'IORH_QUANTITY_4'               => 'numeric',
        'IORH_QUANTITY_5'               => 'numeric',
        'IORH_QUANTITY_6'               => 'numeric',
        'IORH_QUANTITY_7'               => 'numeric',
        'IORH_LENGTH'                   => 'numeric',
        'IORH_WIDTH'                    => 'numeric',
        'IORH_HEIGHT'                   => 'numeric',
        'IORH_NUM_1'                    => 'numeric',
        'IORH_NUM_2'                    => 'numeric',
        'IORH_NUM_3'                    => 'numeric',
        'IORH_NUM_4'                    => 'numeric',
        'IORH_NUM_5'                    => 'numeric',
        'IORH_NUM_6'                    => 'numeric',
        'IORH_NUM_7'                    => 'numeric',
        'IORH_NUM_8'                    => 'numeric',
        'IORH_NUM_9'                    => 'numeric',
        'IORH_NUM_10'                   => 'numeric',
        'IORH_TEXT_1'                   => 'string|max:255',
        'IORH_TEXT_2'                   => 'string|max:255',
        'IORH_TEXT_3'                   => 'string|max:255',
        'IORH_TEXT_4'                   => 'string|max:255',
        'IORH_TEXT_5'                   => 'string|max:255',
        'IORH_TEXT_6'                   => 'string|max:255',
        'IORH_TEXT_7'                   => 'string|max:255',
        'IORH_TEXT_8'                   => 'string|max:255',
        'IORH_TEXT_9'                   => 'string|max:255',
        'IORH_TEXT_10'                  => 'string|max:255',
        'IORH_TASKFIELDS'               => 'string|max:255',
        'IORH_ALTERNATIVE_DEPOTS'       => 'string|max:255',
        'IORH_PICKUP_EXTID1'            => 'string|max:50',
        'IORH_PICKUP_EARLIEST_DATETIME' => 'string|date_format:Ymd',
        'IORH_PICKUP_LATEST_DATETIME'   => 'string|date_format:Ymd',
        'IORH_DELVRY_EXTID1'            => 'string|max:50',
        'IORH_DELVRY_EARLIEST_DATETIME' => 'string|date_format:Ymd',
        'IORH_DELVRY_LATEST_DATETIME'   => 'string|date_format:Ymd',
    ];

    /**
     * Because of how the validator works all the error messages returned the :attribute field would put in spaces
     * afther every letter, we dont want that, so we specify each field what name it must be.
     *
     * @var array
     */
    protected $validationAttributeNames = [
        'IORH_IMPH_REFERENCE'           => 'IORH_IMPH_REFERENCE',
        'IORH_ORDER_TYPE'               => 'IORH_ORDER_TYPE',
        'IORH_EXTID2'                   => 'IORH_EXTID2',
        'IORH_CUSTOMER_ORDER_EXTID'     => 'IORH_CUSTOMER_ORDER_EXTID',
        'IORH_NOTE'                     => 'IORH_NOTE',
        'IORH_ASSORTMENT'               => 'IORH_ASSORTMENT',
        'IORH_CUSTOMER'                 => 'IORH_CUSTOMER',
        'IORH_CONSIGNMENT_EXTID'        => 'IORH_CONSIGNMENT_EXTID',
        'IORH_LOGISTIC_EXTID'           => 'IORH_LOGISTIC_EXTID',
        'IORH_PRECOMBINED_TOUR'         => 'IORH_PRECOMBINED_TOUR',
        'IORH_PREASSIGNED_TRUCK'        => 'IORH_PREASSIGNED_TRUCK',
        'IORH_VEHICLEREQUIREMENTS'      => 'IORH_VEHICLEREQUIREMENTS',
        'IORH_PRIORITY'                 => 'IORH_PRIORITY',
        'IORH_CODRIVER_NEEDED'          => 'IORH_CODRIVER_NEEDED',
        'IORH_TRANSPORTPERIOD'          => 'IORH_TRANSPORTPERIOD',
        'IORH_SOLO'                     => 'IORH_SOLO',
        'IORH_WEIGHT'                   => 'IORH_WEIGHT',
        'IORH_VOLUME'                   => 'IORH_VOLUME',
        'IORH_LOADMETER'                => 'IORH_LOADMETER',
        'IORH_QUANTITY_1'               => 'IORH_QUANTITY_1',
        'IORH_QUANTITY_2'               => 'IORH_QUANTITY_2',
        'IORH_QUANTITY_3'               => 'IORH_QUANTITY_3',
        'IORH_QUANTITY_4'               => 'IORH_QUANTITY_4',
        'IORH_QUANTITY_5'               => 'IORH_QUANTITY_5',
        'IORH_QUANTITY_6'               => 'IORH_QUANTITY_6',
        'IORH_QUANTITY_7'               => 'IORH_QUANTITY_7',
        'IORH_LENGTH'                   => 'IORH_LENGTH',
        'IORH_WIDTH'                    => 'IORH_WIDTH',
        'IORH_HEIGHT'                   => 'IORH_HEIGHT',
        'IORH_NUM_1'                    => 'IORH_NUM_1',
        'IORH_NUM_2'                    => 'IORH_NUM_2',
        'IORH_NUM_3'                    => 'IORH_NUM_3',
        'IORH_NUM_4'                    => 'IORH_NUM_4',
        'IORH_NUM_5'                    => 'IORH_NUM_5',
        'IORH_NUM_6'                    => 'IORH_NUM_6',
        'IORH_NUM_7'                    => 'IORH_NUM_7',
        'IORH_NUM_8'                    => 'IORH_NUM_8',
        'IORH_NUM_9'                    => 'IORH_NUM_9',
        'IORH_NUM_10'                   => 'IORH_NUM_10',
        'IORH_TEXT_1'                   => 'IORH_TEXT_1',
        'IORH_TEXT_2'                   => 'IORH_TEXT_2',
        'IORH_TEXT_3'                   => 'IORH_TEXT_3',
        'IORH_TEXT_4'                   => 'IORH_TEXT_4',
        'IORH_TEXT_5'                   => 'IORH_TEXT_5',
        'IORH_TEXT_6'                   => 'IORH_TEXT_6',
        'IORH_TEXT_7'                   => 'IORH_TEXT_7',
        'IORH_TEXT_8'                   => 'IORH_TEXT_8',
        'IORH_TEXT_9'                   => 'IORH_TEXT_9',
        'IORH_TEXT_10'                  => 'IORH_TEXT_10',
        'IORH_TASKFIELDS'               => 'IORH_TASKFIELDS',
        'IORH_ALTERNATIVE_DEPOTS'       => 'IORH_ALTERNATIVE_DEPOTS',
        'IORH_PICKUP_EXTID1'            => 'IORH_PICKUP_EXTID1',
        'IORH_PICKUP_EARLIEST_DATETIME' => 'IORH_PICKUP_EARLIEST_DATETIME',
        'IORH_PICKUP_LATEST_DATETIME'   => 'IORH_PICKUP_LATEST_DATETIME',
        'IORH_DELVRY_EXTID1'            => 'IORH_DELVRY_EXTID1',
        'IORH_DELVRY_EARLIEST_DATETIME' => 'IORH_DELVRY_EARLIEST_DATETIME',
        'IORH_DELVRY_LATEST_DATETIME'   => 'IORH_DELVRY_LATEST_DATETIME',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'IORH_ORDER_HEADER';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'IORH_IMPH_REFERENCE';

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
     * Indicates if timestamp columns are present in the table.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Imphimporheader()
    {
        return $this->belongsTo(Imph_import_header::class, 'IORH_IMPH_REFERENCE', 'IMPH_REFERENCE');
    }
}
