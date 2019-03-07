<?php

namespace Noxxie\Ptv;

use BadMethodCallException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Noxxie\Ptv\Contracts\Order as DefaultOrderContract;
use Noxxie\Ptv\Exceptions\InvalidAttributeException;
use Noxxie\Ptv\Exceptions\InvalidModelException;
use Noxxie\Ptv\Exceptions\ModelValidationException;
use Noxxie\Ptv\Models\Imph_import_header;
use Noxxie\Ptv\Models\Iora_order_actionpoint;
use Noxxie\Ptv\Models\Iorh_order_header;
use Noxxie\Ptv\Traits\defaultAttributes;
use Noxxie\Ptv\Traits\friendlyAttributes;

class Order implements DefaultOrderContract
{
    use defaultAttributes, friendlyAttributes;

    /**
     * Contains the header model when it is valid.
     *
     * @var null|\Noxxie\Ptv\Models\Imph_import_header;
     */
    public $headerModel = null;

    /**
     * Contains the order header model when it is valid.
     *
     * @var null|\Noxxie\Ptv\Models\Iorh_order_header;
     */
    public $orderHeaderModel = null;

    /**
     * Contains the order actionpoint header when it is valid.
     *
     * @var null|\Noxxie\Ptv\Models\Iora_order_actionpoint;
     */
    public $orderActionpointModel = null;

    /**
     * Contain all the attributes that are used to create this order.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * When the user resolves the class from the service container we allow that it can be resolved
     * and execute a create, update or delete statement.
     *
     * @param string                         $type
     * @param \Illuminate\Support\Collection $attributes
     */
    public function __construct(string $type = null, Collection $attributes = null)
    {
        if (is_null($type) or is_null($attributes)) {
            return;
        }

        // Check if method exists
        if (!method_exists($this, $type)) {
            throw new BadMethodCallException(sprintf('Method "%s" could not be found', $type));
        }

        // Execute the method call
        $this->outcome = call_user_func([$this, $type], $attributes);
    }

    /**
     * Create a new order in the PTV database.
     *
     * @param \Illuminate\Support\Collection $attributes
     * @param bool                           $directSave
     *
     * @return bool
     */
    public function create(Collection $attributes, bool $directSave = true)
    {
        // Fill the needed attributes
        $this->fillAttributes($attributes);

        // Insert the import header
        $header = (new Imph_import_header())->fill($this->attributes['IMPH_IMPORT_HEADER']);

        if ($directSave) {
            if (!$header->save()) {
                throw new ModelValidationException($header->GetErrors());
            }
        } else {
            if (!$header->isValid()) {
                throw new ModelValidationException($header->GetErrors());
            }
            $this->headerModel = $header;
        }

        // Insert the order header
        $orderHeader = (new Iorh_order_header())->fill($this->attributes['IORH_ORDER_HEADER']);

        if ($directSave) {
            if (!$orderHeader->save()) {
                // Destroy the import_header record, if we do the database will remove the rest of the import data (cascading)
                Imph_Import_header::destroy($this->attributes['IMPH_IMPORT_HEADER']['IMPH_REFERENCE']);

                throw new ModelValidationException($orderHeader->getErrors());
            }
        } else {
            if (!$orderHeader->isValid()) {
                throw new ModelValidationException($orderHeader->GetErrors());
            }
            $this->orderHeaderModel = $orderHeader;
        }

        // Inser the order actionpoint
        $orderActionpoint = (new Iora_order_actionpoint())->fill($this->attributes['IORA_ORDER_ACTIONPOINT']);

        if ($directSave) {
            if (!$orderActionpoint->save()) {
                // Destroy the import_header record, if we do the database will remove the rest of the import data (cascading)
                Imph_Import_header::destroy($this->attributes['IMPH_IMPORT_HEADER']['IMPH_REFERENCE']);

                throw new ModelValidationException(($orderActionpoint->getErrors()));
            }
        } else {
            if (!$orderActionpoint->isValid()) {
                throw new ModelValidationException($orderActionpoint->GetErrors());
            }
            $this->orderActionpointModel = $orderActionpoint;
        }

        // When everything is correct update the inserted imph_import_header to 20 so that PTV knows it can import the record
        if ($directSave) {
            $header->update(['IMPH_PROCESS_CODE' => '20']);
        }

        // And we are done
        return true;
    }

    /**
     * Update an existing order in PTV.
     *
     * @param \Illuminate\Support\Collection $attributes
     * @param bool                           $directSave
     *
     * @return bool
     */
    public function update(Collection $attributes, bool $directSave = true)
    {
        // Add the UPDATE command, the rest is the same is the create command
        $attributes['IMPH_ACTION_CODE'] = 'UPDATE';

        // Just execute the create command and we are fine
        return $this->create($attributes, $directSave);
    }

    /**
     * Delete an existing order in PTV.
     *
     * @param \Illuminate\Support\Collection $attributes
     * @param bool                           $directSave
     *
     * @return bool
     */
    public function delete(Collection $attributes, bool $directSave)
    {
        // Add the DELETE command as attribute to the array
        $attributes['IMPH_ACTION_CODE'] = 'DELETE';

        // Fill the needed attributes
        $this->fillAttributes($attributes);

        // Insert the import header
        $header = (new Imph_import_header())->fill($this->attributes['IMPH_IMPORT_HEADER']);

        if ($directSave) {
            if (!$header->save()) {
                throw new ModelValidationException($header->GetErrors());
            }

            // When everything is correct update the inserted imph_import_header to 20 so that PTV knows it can import the record
            $header->update(['IMPH_PROCESS_CODE' => '20']);
        } else {
            if (!$header->isValid()) {
                throw new ModelValidationException($header->GetErrors());
            }
            $this->headerModel = $header;
        }

        // And we are done
        return true;
    }

    /**
     * Main method to fill the attributes variable with correct data.
     *
     * @param \Illuminate\Support\Collection $attributes
     *
     * @return void
     */
    protected function fillAttributes(Collection $attributes)
    {
        // First fill up the attributes array with default values specified in the config
        $this->fillWithDefaultAttributes();

        foreach ($attributes as $column => $data) {
            // Check if it is a friendly naming if so convert it to the normal name
            if ($friendly = $this->isFriendlyAttribute($column)) {
                $this->attributes[$friendly->get('table')][$friendly->get('column')] = $data;
                continue;
            }

            // if it is not friendly it needs to be a value that is linked to a column check that
            if ($default = $this->isColumnAttribute($column)) {
                $this->attributes[$default->get('table')][$default->get('column')] = $data;
                continue;
            }

            // We did not find a friendly or a column attribute, we do not know what te do it with it, throw an exception
            throw new InvalidAttributeException(sprintf('The specified attribute "%s" could not be found in any table model', $column));
        }
    }

    /**
     * This allows the insertion of multi records into the transfer database at once this option only works when the save / update or delete method
     * option $directSave is set to false.
     *
     * @param \Illuminate\Support\Collection $collection
     *
     * @return void
     */
    public static function massSave(Collection $collection)
    {
        $models = [
            'header'           => [],
            'orderheader'      => [],
            'orderactionpoint' => [],
        ];

        $references = [];

        foreach ($collection as $orderInstance) {
            // Check if the instance is an order model
            if (!$orderInstance instanceof self) {
                throw new InvalidModelException('The specified instance is not of the model type "order"');
            }

            // Check if every all the correct models are defined
            if (is_null($orderInstance->headerModel)) {
                throw new InvalidModelException('The specified instance was of type "order" except not all sub models were correctly defined');
            }

            // When the delete method is specified only a header model is needed
            if ($orderInstance->headerModel->IMPH_ACTION_CODE != 'DELETE') {
                if (is_null($orderInstance->orderHeaderModel) || is_null($orderInstance->orderActionpointModel)) {
                    throw new InvalidModelException('The specified instance was of type "order" except not all sub models were correctly defined');
                }
            }

            // Set the header model instance and grab the reference for mass update later
            $models['header'][] = $orderInstance->headerModel->getAttributes();
            $references[] = $orderInstance->headerModel->IMPH_REFERENCE;

            // When the delete method is specified only a header model is needed
            if ($orderInstance->headerModel->IMPH_ACTION_CODE != 'DELETE') {
                $models['orderheader'][] = $orderInstance->orderHeaderModel->getAttributes();
                $models['orderactionpoint'][] = $orderInstance->orderActionpointModel->getAttributes();
            }
        }

        // Well we have all the models we want now insert them
         // Well we have all the models we want now insert them
        foreach (array_chunk($models['header'], 100) as $chunk) {
            Imph_import_header::insert($chunk);
        }

        foreach (array_chunk($models['orderheader'], 100) as $chunk) {
            Iorh_order_header::insert($chunk);
        }

        foreach (array_chunk($models['orderactionpoint'], 100) as $chunk) {
            Iora_order_actionpoint::insert($chunk);
        }

        // and now update all the records that they may be imported into PTV
        DB::connection(config('ptv.connection'))
                    ->table($collection[0]->headerModel->getTable())
                    ->whereIn('IMPH_REFERENCE', $references)
                    ->update(['IMPH_PROCESS_CODE' => '20']);

        return true;
    }
}
