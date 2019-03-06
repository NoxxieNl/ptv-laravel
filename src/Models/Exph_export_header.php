<?php

namespace Noxxie\ptv\models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Noxxie\Ptv\Models\Etps_tour_stop;
use Noxxie\Ptv\Models\Etpt_tour_header;

class Exph_export_header extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'EXPH_EXPORT_HEADER';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'EXPH_REFERENCE';

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
    protected $fillable = ['EXPH_CONTEXT', 'EXPH_OBJECT_TYPE', 'EXPH_ACTION_CODE', 'EXPH_EXTID', 'EXPH_REFERENCECURRENCY', 'EXPH_EXPORT_COUNT', 'EXPH_PROCESS_CODE', 'EXPH_CREATION_TIME', 'EXPH_PROCESS_TIME', 'EXPH_PROCESS_RETRIES'];

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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'EXPH_REFERENCE' => 'integer',
    ];

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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function etpstourstops()
    {
        return $this->hasMany(Etps_tour_stop::class, 'ETPS_EXPH_REFERENCE', 'EXPH_REFERENCE');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function etpttourheader()
    {
        return $this->hasOne(Etpt_tour_header::class, 'ETPT_EXPH_REFERENCE', 'EXPH_REFERENCE');
    }

    /**
     * Scope a query to only include non imported routes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $code
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithProcessCode(Builder $query, string $code)
    {
        return $query->where('EXPH_PROCESS_CODE', $code);
    }

    /**
     * Scope a query to only include non imported routes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereNotImported(Builder $query)
    {
        return $this->scopeWithProcessCode($query, '20');
    }

    /**
     * Scope a query to only include specific action types.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null                           $code
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereAction(Builder $query, ?string $type)
    {
        if (is_null($type)) {
            return $query;
        }

        return $query->where('EXPH_ACTION_CODE', strtoupper($type));
    }

    /**
     * Scope a query to automaticly filter on reference.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereReference(Builder $query, string $id)
    {
        return $query->where('EXPH_EXTID', $id);
    }
}
