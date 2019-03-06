<?php
namespace Noxxie\Ptv\Models;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RouteDetail extends Model {

    protected $guarded = [];

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d\TH:i:sP';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'arrival_time' => 'datetime',
        'start_service_time' => 'datetime',
        'end_service_time' => 'datetime',
        'departure_time' => 'datetime',
        'starting_time' => 'datetime',
        'ending_time'  => 'datetime'
    ];

    /**
     * Overwrite the default create function of a model, because we are injecting it with an existing one and transform it to a default format
     *
     * @param \Noxxie\ptv\models\Etpa_tour_actionpoint $header
     * @return \Noxxie\Ptv\Models\Route
     */
    static public function create(\stdClass $detail)
    {
        // We want the attributes to be lowercased and the removal of the prefix 'etpa_' on the attributes
        $attributes = [];
        foreach ($detail as $name => $value)
        {
            $attributes[str_replace('etpa_', '', strtolower($name))] = $value;
        }
        // Create the new model
        $model = new RouteDetail($attributes);
        $model->original_model = $detail;

        // Return the model
        return $model;
    }

    /**
     * Clean up invalid UTF-8 encoding for street attribute
     *
     * @param string|null $value
     * @return void
     */
    public function setStreetAttribute(?string $value)
    {
        $this->attributes['street'] = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
    }

    /**
     * Clean up invalid UTF-8 encoding for houseno attribute
     *
     * @param string|null $value
     * @return void
     */
    public function setHousenoAttribute(?string $value)
    {
        $this->attributes['houseno'] = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
    }

    /**
     * Clean up invalid UTF-8 encoding for city attribute
     *
     * @param string|null $value
     * @return void
     */
    public function setCityAttribute(?string $value)
    {
        $this->attributes['city'] = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
    }

    /**
     * Clean up invalid UTF-8 encoding for postcode attribute
     *
     * @param string|null $value
     * @return void
     */
    public function setPostcodeAttribute(?string $value)
    {
        $this->attributes['postcode'] = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
    }

    /**
     * Returns the reference of the detail record
     *
     * @return mixed
     */
    public function getReferenceAttribute()
    {
        return $this->order_extid1;
    }

    /**
     * Returns the order number for this detail record
     *
     * @return int
     */
    public function getSequenceNumberAttribute()
    {
        return $this->etps_tourpoint_sequence;
    }
}
