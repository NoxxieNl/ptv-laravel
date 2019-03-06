<?php
namespace Noxxie\Ptv;
use Noxxie\Ptv\Contracts\Order as DefaultOrderContract;
use Illuminate\Support\Collection;
use Noxxie\Ptv\Models\Imph_import_header;
use Noxxie\Ptv\Models\Iorh_order_header;
use Noxxie\Ptv\Models\Iora_order_actionpoint;
use Noxxie\Ptv\Traits\defaultAttributes;
use Noxxie\Ptv\Traits\friendlyAttributes;
use Noxxie\Ptv\Exceptions\InvalidAttributeException;
use Noxxie\Ptv\Exceptions\ModelValidationException;
use \BadMethodCallException;

class Order implements DefaultOrderContract
{
    use defaultAttributes, friendlyAttributes;

    /**
     * Contain all the attributes that are used to create this order
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * When the user resolves the class from the service container we allow that it can be resolved
     * and execute a create, update or delete statement
     *
     * @param string $type
     * @param \Illuminate\Support\Collection $attributes
     */
    public function __construct(string $type = null, Collection $attributes = null) {
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
     * Create a new order in the PTV database
     *
     * @param \Illuminate\Support\Collection $attributes
     * @return bool
     */
    public function create(Collection $attributes)
    {
        // Fill the needed attributes
        $this->fillAttributes($attributes);

        // Insert the import header
        $header = (new Imph_import_header())->fill($this->attributes['IMPH_IMPORT_HEADER']);
        if (! $header->save()) {
            throw new ModelValidationException($header->GetErrors());
        }

        // Insert the order header
        $orderHeader = (new Iorh_order_header())->fill($this->attributes['IORH_ORDER_HEADER']);
        if (! $orderHeader->save()) {
            // Destroy the import_header record, if we do the database will remove the rest of the import data (cascading)
            Imph_Import_header::destroy($this->attributes['IMPH_IMPORT_HEADER']['IMPH_REFERENCE']);
            throw new ModelValidationException($orderHeader->getErrors());
        }

        // Inser the order actionpoint
        $orderActionpoint = (new Iora_order_actionpoint())->fill($this->attributes['IORA_ORDER_ACTIONPOINT']);
        if (! $orderActionpoint->save()) {
            // Destroy the import_header record, if we do the database will remove the rest of the import data (cascading)
            Imph_Import_header::destroy($this->attributes['IMPH_IMPORT_HEADER']['IMPH_REFERENCE']);
            throw new ModelValidationException(($orderActionpoint->getErrors()));
        }

        // When everything is correct update the inserted imph_import_header to 20 so that PTV knows it can import the record
        $header->update(['IMPH_PROCESS_CODE' => '20']);

        // And we are done
        return true;
    }

    /**
     * Update an existing order in PTV
     *
     * @param \Illuminate\Support\Collection $attributes
     * @return bool
     */
    public function update(Collection $attributes)
    {
        // Add the UPDATE command, the rest is the same is the create command
        $attributes['IMPH_ACTION_CODE'] = 'UPDATE';

        // Just execute the create command and we are fine
        return $this->create($attributes);
    }

    /**
     * Delete an existing order in PTV
     *
     * @param \Illuminate\Support\Collection $attributes
     * @return bool
     */
    public function delete(Collection $attributes)
    {
        // Add the DELETE command as attribute to the array
        $attributes['IMPH_ACTION_CODE'] = 'DELETE';

        // Fill the needed attributes
        $this->fillAttributes($attributes);

        // Insert the import header
        $header = (new Imph_import_header())->fill($this->attributes['IMPH_IMPORT_HEADER']);
        if (! $header->save()) {
            throw new ModelValidationException($header->GetErrors());
        }

        // When everything is correct update the inserted imph_import_header to 20 so that PTV knows it can import the record
        $header->update(['IMPH_PROCESS_CODE' => '20']);

        // And we are done
        return true;
    }

    /**
     * Main method to fill the attributes variable with correct data
     *
     * @param \Illuminate\Support\Collection $attributes
     * @return void
     */
    protected function fillAttributes(Collection $attributes)
    {
        // First fill up the attributes array with default values specified in the config
        $this->fillWithDefaultAttributes();

        foreach ($attributes as $column => $data)
        {
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
}
