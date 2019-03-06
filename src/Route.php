<?php
namespace Noxxie\Ptv;

use Noxxie\Ptv\Contracts\Route as DefaultRouteContract;
use Noxxie\Ptv\Models\Exph_export_header;
use Noxxie\Ptv\Models\Route as RouteModel;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Noxxie\Ptv\Exceptions\InvalidTypeArgumentException;
use Noxxie\Ptv\Exceptions\InvalidModelException;
use \InvalidArgumentException;

class Route implements DefaultRouteContract {

    /**
     * Retrieve a route by its reference, if more then one export of the route is found only the most recent will be retrieved
     *
     * @param int $id
     * @param bool $latestOnly
     * @return void
     */
    public function get(int $id, bool $latestOnly = true) {

        // Search for the given $id, if the given $id has more then one record in the database return the most recent one
        if (is_null($result = Exph_export_header::whereReference($id)->orderBy('EXPH_CREATION_TIME', 'desc')->get())) {
            return null;
        }

        /*
            When latestOnly is specified as true there will always be one RouteModel returned
            when it is specified as false a eloquent collection will always be returned regardless if there is only one record found
        */
        if ($latestOnly) {
            $result = $result->first();
            return RouteModel::create($result);
        } else {
            $collection = new EloquentCollection;
            $result->each(function ($instance) use (&$collection) {
                $collection->push(RouteModel::create($instance));
            });

            return $collection;
        }
    }

    /**
     * Retrieve all the routes that are not marked as imported
     *
     * @param string|null $type
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getNotImported(?string $type = null) {

        // Check if the given type is a valid type
        if (!is_null($type) and !in_array(strtoupper($type), ['NEW', 'UPDATE', 'DELETE'])) {
            throw new InvalidTypeArgumentException(sprintf('The specified type "%s" is not a valid type', $type));
        }

        // Search for not imported routes, optional with the specified type
        if (is_null($results = Exph_export_header::whereNotImported()->whereAction($type)->get())) {
            return null;
        }

        // Collect all the route data and serve it back to the user
        $routes = RouteModel::create($results);
        return $routes;
    }

    /**
     * Mark a route with the specified code
     *
     * @param \Noxxie\Ptv\Models\Route|Illuminate\Database\Eloquent\Collection $data
     * @param int $code
     *
     * @return bool
     */
    public function markAs($data, int $code)
    {
        if (!$data instanceof EloquentCollection and !$data instanceof RouteModel) {
            throw new InvalidModelException(sprintf('The specified instance is not an instance of "%s"', RouteModel::class));
        }

        if (!in_array($code, [20, 30, 50, -30])) {
            throw new InvalidArgumentException(sprintf('The specified code "%s" is not a valid code', $code));
        }

        if ($data instanceof EloquentCollection) {
            // We do not want to execute any update when a model inside the collection is not a RouteModel
            $data->each(function ($instance) {
                if (!$instance instanceof RouteModel) {
                    throw new InvalidModelException(sprintf('The specified instance is not an instance of "%s"', RouteModel::class));
                }
            });

            // Loop all the model instances and update the the specified code
            $data->each(function ($instance) use ($code) {
                // When creating the route model the original model is availible to us, so we can easily update the processing status
                $instance->original_model->exph_process_code = $code;
                $instance->original_model->save();
            });
        } else {
            $data->original_model->exph_process_code = $code;
            $data->original_model->save();
        }

        return true;
    }

    /**
     * Helper function for easy update route as imported
     *
     * @param \Noxxie\Ptv\Models\Route|Illuminate\Database\Eloquent\Collection $data
     * @return bool
     */
    public function markAsImported($data)
    {
        return $this->markAs($data, 50);
    }

    /**
     * Helper function for easy update route as failed
     *
     * @param \Noxxie\Ptv\Models\Route|Illuminate\Database\Eloquent\Collection $data
     * @return bool
     */
    public function markAsFailed($data)
    {
        return $this->markAs($data, -30);
    }
}
