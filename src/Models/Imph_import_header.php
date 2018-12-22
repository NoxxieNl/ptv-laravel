<?php

namespace Noxxie\Ptv\Models;

use Illuminate\Database\Eloquent\Model;

use Noxxie\Ptv\Models\Iorh_order_header;
use Noxxie\Ptv\Models\Iora_order_actionpoint;

use Noxxie\Ptv\Traits\defaultAttributes;
use Watson\Validating\ValidatingTrait;

class Imph_import_header extends Model
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
        $this->rules['IMPH_REFERENCE'] = str_replace('ptv.', config('ptv.connection') . '.', $this->rules['IMPH_REFERENCE']);

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
        'IMPH_REFERENCE' => 'unique:ptv.IMPH_IMPORT_HEADER,IMPH_REFERENCE|required|integer|digits_between:0,999999999',
        'IMPH_CONTEXT' => 'required|string|max:20',
        'IMPH_OBJECT_TYPE' => 'required|string',
        'IMPH_ACTION_CODE' => 'required|in:NEW,UPDATE,DELETE',
        'IMPH_EXTID' => 'required|string|max:50',
        'IMPH_PROCESS_CODE' => 'required|integer|in:0,10,20',
        'IMPH_CREATION_TIME' => 'required|date_format:Ymd'
    ];

    /**
     * Because of how the validator works all the error messages returned the :attribute field would put in spaces
     * afther every letter, we dont want that, so we specify each field what name it must be
     *
     * @var array
     */
    protected $validationAttributeNames = [
        'IMPH_REFERENCE' => 'IMPH_REFERENCE',
        'IMPH_CONTEXT' => 'IMPH_CONTEXT',
        'IMPH_OBJECT_TYPE' => 'IMPH_OBJECT_TYPE',
        'IMPH_ACTION_CODE' => 'IMPH_ACTION_CODE',
        'IMPH_EXTID' => 'IMPH_EXTID',
        'IMPH_PROCESS_CODE' => 'IMPH_PROCESS_CODE',
        'IMPH_CREATION_TIME' => 'IMPH_CREATION_TIME'
    ];

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'IMPH_IMPORT_HEADER';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'IMPH_REFERENCE';

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
     * Indicates if timestamp columns are present in the table
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['IMPH_CONTEXT', 'IMPH_OBJECT_TYPE', 'IMPH_ACTION_CODE', 'IMPH_EXTID', 'IMPH_PROCESS_CODE', 'IMPH_CREATION_TIME', 'IMPH_PROCESS_TIME', 'IMPH_PROCESS_RETRIES', 'IMPH_DESCRIPTION'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ioraorderactionpoints()
    {
        return $this->hasMany(Iora_order_actionpoint::class, 'IORA_IMPH_REFERENCE', 'IMPH_REFERENCE');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function iorhorderheader()
    {
        return $this->hasOne(Iorh_order_header::class, 'IORH_IMPH_REFERENCE', 'IMPH_REFERENCE');
    }
}
