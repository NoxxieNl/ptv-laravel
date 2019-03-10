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
     * Contains all the validated orders that are ready for insertion.
     *
     * @var array
     */
    public $preparedOrders = [];

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
     * @param string $type
     * @param array  $attributes
     */
    public function __construct(string $type = null, array $attributes = null)
    {
        if (is_null($type) or is_null($attributes)) {
            return;
        }

        // Check if method exists
        if (!method_exists($this, $type)) {
            throw new BadMethodCallException(sprintf('Method "%s" could not be found', $type));
        }

        // Execute the method call
        $method = call_user_func([$this, $type], $attributes);

        return $method->save();
    }

    /**
     * Create a new order in the PTV database.
     *
     * @param array $attributes
     *
     * @return bool
     */
    public function create(array $attributes)
    {
        // Convert to collection
        $attributes = collect($attributes);

        // Check if a multidimensional array was specified if not convert the single one that it looks like a multidimensional array
        if (!$attributes->has(0)) {
            $attributes = collect([
                $attributes->toArray(),
            ]);
        }

        // Loop every attribute group
        foreach ($attributes as $attributeGroup) {
            // Make the attributeGroup a collection
            $attributeGroup = collect($attributeGroup);

            // Fill the needed attributes
            $this->fillAttributes($attributeGroup);

            // Insert the import header
            $header = (new Imph_import_header())->fill($this->attributes['IMPH_IMPORT_HEADER']);

            // Check if the defined data is valid for insertion
            if (!$header->isValid()) {
                throw new ModelValidationException($header->GetErrors());
            }

            // Insert the order header
            $orderHeader = (new Iorh_order_header())->fill($this->attributes['IORH_ORDER_HEADER']);

            // Check if the data is valid for insertion
            if (!$orderHeader->isValid()) {
                throw new ModelValidationException($orderHeader->GetErrors());
            }

            // Inser the order actionpoint
            $orderActionpoint = (new Iora_order_actionpoint())->fill($this->attributes['IORA_ORDER_ACTIONPOINT']);

            // Check if the data is valid for insertion
            if (!$orderActionpoint->isValid()) {
                throw new ModelValidationException($orderActionpoint->GetErrors());
            }

            // And we are done
            $this->preparedOrders[] = [
                'header'           => $header,
                'orderheader'      => $orderHeader,
                'orderactionpoint' => $orderActionpoint,
            ];
        }

        return $this;
    }

    /**
     * Update an existing order in PTV.
     *
     * @param array $attributes
     *
     * @return bool
     */
    public function update(array $attributes)
    {
        /*
            Add the UPDATE command, the rest is the same is the create command
            When it is a multidimensional loop all the arrays and update the action code to update
        */
        if (isset($attributes[0])) {
            foreach ($attributes as $attributeGroupIndex => $attributeGroup) {
                $attributes[$attributeGroupIndex]['IMPH_ACTION_CODE'] = 'UPDATE';
            }
        } else {
            $attributes['IMPH_ACTION_CODE'] = 'UPDATE';
        }

        // Just execute the create command and we are fine
        return $this->create($attributes);
    }

    /**
     * Delete an existing order in PTV.
     *
     * @param array $attributes
     *
     * @return bool
     */
    public function delete(array $attributes)
    {
        // Convert to collection
        $attributes = collect($attributes);

        // Check if a multidimensional array was specified if not convert the single one that it looks like a multidimensional array
        if (!$attributes->has(0)) {
            $attributes = collect([
                $attributes->toArray(),
            ]);
        }

        foreach ($attributes as $attributeGroup) {

            // Add the DELETE command as attribute to the array
            $attributeGroup['IMPH_ACTION_CODE'] = 'DELETE';

            // Make the attributeGroup a collection
            $attributeGroup = collect($attributeGroup);

            // Fill the needed attributes
            $this->fillAttributes($attributeGroup);

            // Insert the import header
            $header = (new Imph_import_header())->fill($this->attributes['IMPH_IMPORT_HEADER']);

            // Check if the data is valid for insertion
            if (!$header->isValid()) {
                throw new ModelValidationException($header->GetErrors());
            }

            // And we are done
            $this->preparedOrders[] = [
                'header' => $header,
            ];
        }

        return $this;
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
        $this->fillWithDefaultAttributes(true);

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
     * Save the prepared orders (Does not mather if it is a create, update or delete. The different methods may be combined into one save).
     *
     * @return void
     */
    public function save()
    {
        // Define needed arrays for this method
        $modelAttributes = [
            'header'           => [],
            'orderheader'      => [],
            'orderactionpoint' => [],
        ];

        $references = [];

        // Check if we have anything to save
        if (count($this->preparedOrders) == 0) {
            throw new BadMethodCallException('No prepared orders found use the create / update / delete method first before useing save');
        }

        // Loop every specified order
        foreach ($this->preparedOrders as $order) {

            // When the delete method is specified only a header model is needed
            if ($order['header']->IMPH_ACTION_CODE != 'DELETE') {
                if (is_null($order['orderheader']) || is_null($order['orderactionpoint'])) {
                    throw new InvalidModelException('The specified instance was of type "order" except not all sub models were correctly defined');
                }
            }

            // Set the header model instance and grab the reference for mass update later
            $modelAttributes['header'][] = $order['header']->getAttributes();
            $references[] = $order['header']->IMPH_REFERENCE;

            // When the delete method is specified only a header model is needed
            if ($order['header']->IMPH_ACTION_CODE != 'DELETE') {
                $modelAttributes['orderheader'][] = $order['orderheader']->getAttributes();
                $modelAttributes['orderactionpoint'][] = $order['orderactionpoint']->getAttributes();
            }
        }

        // Well we have all the models we want now insert them
        foreach (array_chunk($modelAttributes['header'], 100) as $chunk) {
            Imph_import_header::insert($chunk);
        }

        foreach (array_chunk($modelAttributes['orderheader'], 100) as $chunk) {
            Iorh_order_header::insert($chunk);
        }

        foreach (array_chunk($modelAttributes['orderactionpoint'], 100) as $chunk) {
            Iora_order_actionpoint::insert($chunk);
        }

        // and now update all the records that they may be imported into PTV
        DB::connection(config('ptv.connection'))
                    ->table($this->preparedOrders[0]['header']->getTable())
                    ->whereIn('IMPH_REFERENCE', $references)
                    ->update(['IMPH_PROCESS_CODE' => '20']);

        return true;
    }
}
