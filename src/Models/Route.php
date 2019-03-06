<?php

namespace Noxxie\Ptv\Models;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Noxxie\Ptv\Exceptions\InvalidModelException;
use Noxxie\ptv\models\Exph_export_header;

class Route extends Model
{
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
        'creation_time' => 'datetime',
    ];

    /**
     * Overwrite the default create function of a model, because we are injecting it with an existing one and transform it to a default format.
     *
     * @param \Noxxie\ptv\models\Exph_export_header|\Illuminate\Database\Eloquent\Collection $data
     *
     * @return \Noxxie\Ptv\Models\Route
     */
    public static function create($data)
    {
        if (!$data instanceof EloquentCollection and !$data instanceof Exph_export_header) {
            throw new InvalidModelException();
        }

        if ($data instanceof \Noxxie\ptv\models\Exph_export_header) {
            return self::createModel($data);
        } else {
            $collection = new EloquentCollection();
            foreach ($data as $instance) {
                $collection->push(self::createModel($instance));
            }

            return $collection;
        }
    }

    /**
     * Create the correct route model that is passed back to the user.
     *
     * @param \Noxxie\ptv\models\Exph_export_header $originalModel
     *
     * @return \Noxxie\Ptv\Models\Route
     */
    protected static function createModel(Exph_export_header $originalModel)
    {
        // We want the attributes to be lowercased and the removal of the prefix 'exph_' on the attributes
        $attributes = collect($originalModel->getAttributes())->flatMap(function ($value, $name) {
            return [str_replace('exph_', '', strtolower($name)) => $value];
        });

        // Create the new model
        $model = new self($attributes->toArray());
        $model->original_model = $originalModel;

        // Create the relationship
        $model->setRouteDetails();

        // Return the model
        return $model;
    }

    /**
     * Manually set the relationship between this model and the RouteDetail models
     * This is so we can speed up the the retrieval of the database query's and give the user the same experience as
     * when he retrieves data from normal models.
     *
     * @return void
     */
    protected function setRouteDetails()
    {
        // When type is delete we do not have any details
        if ($this->action_code == 'DELETE') {
            return;
        }

        // Because of the composit key working of the transfer database we cannot use eloquent models to fetch the correct results
        // The query would slow down by allot
        $actionpoints = DB::connection(config('ptv.connection'))->table('ETPA_TOUR_ACTIONPOINT')->where([
            ['ETPA_ETPS_EXPH_REFERENCE', $this->original_model->etpstourstops->first()->ETPS_EXPH_REFERENCE],
            ['ETPA_LOCATION_FUNCTION', '!=', 'DEPOT'],
        ])->get();

        // Loop all the tour stops, the details are stored in the actionpoint table thats why we are going to need dig a little deeper
        // into the tables and get the correct data we need
        $returnModels = new Collection();
        foreach ($this->original_model->etpstourstops as $tourstop) {
            foreach ($actionpoints as $actionpoint) {
                // Check if we have a unique matching record
                if ($tourstop->ETPS_TOURPOINT_SEQUENCE == $actionpoint->ETPA_ETPS_TOURPOINT_SEQUENCE and
                    $tourstop->ETPS_EXPH_REFERENCE == $actionpoint->ETPA_ETPS_EXPH_REFERENCE) {

                    // Create the RouteDetail model for this record
                    $returnModels->push(RouteDetail::create($actionpoint));
                }
            }
        }

        // Manually set the relationship to the detail record
        $this->setRelation('RouteDetails', $returnModels->sortBy('sequence_number'));
    }
}
