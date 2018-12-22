<?php

namespace Noxxie\Ptv\Models;

use Illuminate\Database\Eloquent\Model;

use Noxxie\Ptv\Models\Imph_import_header;

use Noxxie\Ptv\Traits\defaultAttributes;
use Watson\Validating\ValidatingTrait;

class Iora_order_actionpoint extends Model
{
    use ValidatingTrait, defaultAttributes;

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

        // Correct the rules settings
        $this->rules['IORA_IMPH_REFERENCE'] = str_replace('ptv.', config('ptv.connection') . '.', $this->rules['IORA_IMPH_REFERENCE']);

        // Set default attributes from config
        $this->setDefaultAttributes();

        // Allow parent to do his work
        parent::__construct();
    }

    /**
     * Validation rules for validation the specified information
     * if any of the data is not valid the model will not insert the record
     * 
     * @var array
     */
    protected $rules = [
        'IORA_IMPH_REFERENCE' => 'required|numeric|digits_between:0,999999999|exists:ptv.IMPH_IMPORT_HEADER,IMPH_REFERENCE',
        'IORA_ACTION' => 'required|string|max:20|in:DELIVERY,PICKUP',
        'IORA_EXTID1' => 'required|string|max:50',
        'IORA_EXTID2' => 'string|max:50',
        'IORA_IS_ONETIME' => 'required|in:0,1',
        'IORA_NAME' => 'string|max:50',
        'IORA_DESC' => 'string|max:255',
        'IORA_SHORTDESC' => 'string|max:30',
        'IORA_COUNTRY' => 'required|string|max:3',
        'IORA_STATE' => 'string|max:100',
        'IORA_POSTCODE' => 'required|string|max:12',
        'IORA_CITY' => 'required|string|max:100',
        'IORA_DISTRICT' => 'string|max:80',
        'IORA_STREET' => 'required|string|max:100',
        'IORA_HOUSENO' => 'required|string|max:10',
        'IORA_LATITUDE' => 'integer',
        'IORA_LONGITUDE' => 'integer',
        'IORA_COORD_TYPE' => 'string|max:20|in:GEOMINSEC,SUPERCONFORM,MERCATOR,GEODECIMAL',
        'IORA_EARLIEST_DATETIME' => 'required|date_format:Ymd',
        'IORA_LATEST_DATETIME' => 'required|date_format:Ymd',
        'IORA_SERVICEPERIODEXTERNAL' => 'integer',
        'IORA_OH_TOLERANCE_CLASS' => 'string|in:0,1',
        'IORA_HANDLINGTIME_CLASS' => 'required|integer|digits_between:1,99999',
        'IORA_HANDLINGTIME_ADDITIONAL' => 'integer',
        'IORA_PRECOMBINED_TOUR_SEQUENCE' => 'integer',
        'IORA_TOUR_POS' => 'string|max:20|in:LAST,LASTSECTION,FIRSTSECTION,FIRST,NONE',
        'IORA_SERVICE_IN_INTERVAL' => 'string|in:0,1',
        'IORA_CUSTOMER_INDICATOR' => 'integer',
        'IORA_CUSTOMER_GROUP' => 'integer',
        'IORA_NOTRAILER' => 'string|in:0,1',
        'IORA_CODRIVER_NEEDED' => 'string|in:0,1',
        'IORA_VEHICLEREQUIREMENTS' => 'string|max:255',
        'IORA_MIN_VEHICLE_GROUP' => 'numeric|digits_between:0,99',
        'IORA_MAX_VEHICLE_GROUP' => 'numeric|digits_between:0,99',
        'IORA_ADDITIONAL_VEHICLE_GROUP' => 'string|max:255',
        'IORA_PHONE' => 'string|max:50',
        'IORA_EMAIL' => 'string|max:255',
    ];
    
    /**
     * Because of how the validator works all the error messages returned the :attribute field would put in spaces
     * afther every letter, we dont want that, so we specify each field what name it must be
     *
     * @var array
     */
    protected $validationAttributeNames = [
        'IORA_IMPH_REFERENCE' => 'IORA_IMPH_REFERENCE',
        'IORA_ACTION' => 'IORA_ACTION',
        'IORA_EXTID1' => 'IORA_EXTID1',
        'IORA_EXTID2' => 'IORA_EXTID2',
        'IORA_IS_ONETIME' => 'IORA_IS_ONETIME',
        'IORA_NAME' => 'IORA_NAME',
        'IORA_DESC' => 'IORA_DESC',
        'IORA_SHORTDESC' => 'IORA_SHORTDESC',
        'IORA_COUNTRY' => 'IORA_COUNTRY',
        'IORA_STATE' => 'IORA_STATE',
        'IORA_POSTCODE' => 'IORA_POSTCODE',
        'IORA_CITY' => 'IORA_CITY',
        'IORA_DISTRICT' => 'IORA_DISTRICT',
        'IORA_STREET' => 'IORA_STREET',
        'IORA_HOUSENO' => 'IORA_HOUSENO',
        'IORA_LATITUDE' => 'IORA_LATITUDE',
        'IORA_LONGITUDE' => 'IORA_LONGITUDE',
        'IORA_COORD_TYPE' => 'IORA_COORD_TYPE',
        'IORA_EARLIEST_DATETIME' => 'IORA_EARLIEST_DATETIME',
        'IORA_LATEST_DATETIME' => 'IORA_LATEST_DATETIME',
        'IORA_SERVICEPERIODEXTERNAL' => 'IORA_SERVICEPERIODEXTERNAL',
        'IORA_OH_TOLERANCE_CLASS' => 'IORA_OH_TOLERANCE_CLASS',
        'IORA_HANDLINGTIME_CLASS' => 'IORA_HANDLINGTIME_CLASS',
        'IORA_HANDLINGTIME_ADDITIONAL' => 'IORA_HANDLINGTIME_ADDITIONAL',
        'IORA_PRECOMBINED_TOUR_SEQUENCE' => 'IORA_PRECOMBINED_TOUR_SEQUENCE',
        'IORA_TOUR_POS' => 'IORA_TOUR_POS',
        'IORA_SERVICE_IN_INTERVAL' => 'IORA_SERVICE_IN_INTERVAL',
        'IORA_CUSTOMER_INDICATOR' => 'IORA_CUSTOMER_INDICATOR',
        'IORA_CUSTOMER_GROUP' => 'IORA_CUSTOMER_GROUP',
        'IORA_NOTRAILER' => 'IORA_NOTRAILER',
        'IORA_CODRIVER_NEEDED' => 'IORA_CODRIVER_NEEDED',
        'IORA_VEHICLEREQUIREMENTS' => 'IORA_VEHICLEREQUIREMENTS',
        'IORA_MIN_VEHICLE_GROUP' => 'IORA_MIN_VEHICLE_GROUP',
        'IORA_MAX_VEHICLE_GROUP' => 'IORA_MAX_VEHICLE_GROUP',
        'IORA_ADDITIONAL_VEHICLE_GROUP' => 'IORA_ADDITIONAL_VEHICLE_GROUP',
        'IORA_PHONE' => 'IORA_PHONE',
        'IORA_EMAIL' => 'IORA_EMAIL',
    ];

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'IORA_ORDER_ACTIONPOINT';

    /**
     * Indicates if timestamp columns are present in the table
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['IORA_EXTID1', 'IORA_EXTID2', 'IORA_IS_ONETIME', 'IORA_NAME', 'IORA_DESC', 'IORA_SHORTDESC', 'IORA_COUNTRY', 'IORA_STATE', 'IORA_POSTCODE', 'IORA_CITY', 'IORA_DISTRICT', 'IORA_STREET', 'IORA_HOUSENO', 'IORA_LATITUDE', 'IORA_LONGITUDE', 'IORA_COORD_TYPE', 'IORA_EARLIEST_DATETIME', 'IORA_LATEST_DATETIME', 'IORA_SERVICEPERIODEXTERNAL', 'IORA_OH_TOLERANCE_CLASS', 'IORA_HANDLINGTIME_CLASS', 'IORA_HANDLINGTIME_ADDITIONAL', 'IORA_PRECOMBINED_TOUR_SEQUENCE', 'IORA_TOUR_POS', 'IORA_SERVICE_IN_INTERVAL', 'IORA_CUSTOMER_INDICATOR', 'IORA_CUSTOMER_GROUP', 'IORA_NOTRAILER', 'IORA_CODRIVER_NEEDED', 'IORA_VEHICLEREQUIREMENTS', 'IORA_MIN_VEHICLE_GROUP', 'IORA_MAX_VEHICLE_GROUP', 'IORA_ADDITIONAL_VEHICLE_GROUP', 'IORA_PHONE', 'IORA_EMAIL'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Imphimportheader()
    {
        return $this->belongsTo(Imph_import_header::class, 'IORA_IMPH_REFERENCE', 'IMPH_REFERENCE');
    }
}
