<?php
namespace Noxxie\Ptv;

use Noxxie\Ptv\Models\Imph_import_header;
use Noxxie\Ptv\Models\Iora_order_actionpoint;
use Noxxie\Ptv\Models\Iorh_order_header;

use Noxxie\Ptv\Traits\friendlyAttributes;
use Noxxie\Ptv\Traits\customAttributes;

use Illuminate\Support\collection;

class AddOrder 
{
    use customAttributes, friendlyAttributes;

    /**
     * Contains the errorbag if any
     */
    protected $errors;


    /**
     * Contains all the specified attributes
     */
    protected $attributes;

    /**
     * Create a new order in PTV
     *
     * @param collection $collection
     * @return boolean
     */
    public function create(collection $collection)
    {
        // Set attributes in class
        $this->attributes = $collection;

        // Filter out custom specified attributes and check if they are valid
        $this->filterCustomAttributes($collection);

        if ($this->customAttributeHasError()) {
            $this->errors = $this->customAttributeGetErrors();
            return false;
        }

        // Create a new header
        $importHeader = new Imph_import_header;

        // insert friendly attributes in to the model
        foreach ($this->getFriendlyAttributes('IMPH') as $column => $value)
        {
            $importHeader->$column = $value;
        }

        // insert the custom attributes in to the model
        foreach ($this->getCustomAttributes('IMPH') as $column => $value)
        {
            $importHeader->$column = $value;
        }

        // Validate and save the model
        if (!$importHeader->save()) {
            $this->errors = $importHeader->getErrors();
            return false;
        }

        // Create new order header
        $orderHeader = new Iorh_order_header;
        
        // insert friendly attributes in to the model
        foreach ($this->getFriendlyAttributes('IORH') as $column => $value)
        {
            $orderHeader->$column = $value;
        }

        // insert the custom attributes in to the model
        foreach ($this->getCustomAttributes('IORH') as $key => $value)
        {
            $orderHeader->$key = $value;
        }

        // Validate and save the model
        if (!$orderHeader->save()) {
            $this->errors = $orderHeader->getErrors();

            // Destroy the imph_import_header model, if we do that the database will remove the rest of the data
            $reference = $this->getFriendlyAttributes('IMPH', 'IMPH_REFERENCE') == null ? $this->attributes['IMPH_REFERENCE'] : $this->getFriendlyAttributes('IMPH', 'IMPH_REFERENCE');
            (new Imph_import_header)->destroy($reference);

            return false;
        }

        // Create a new actionpoint
        $actionpoint = new Iora_order_actionpoint;

        // insert friendly attributes in to the model
        foreach ($this->getFriendlyAttributes('IORA') as $column => $value)
        {
            $actionpoint->$column = $value;
        }

        // insert the custom attributes in to the model
        foreach ($this->getCustomAttributes('IORA') as $key => $value)
        {
            $actionpoint->$key = $value;
        }

        // Static function that the extid1 is always filled
        $actionpoint->IORA_EXTID1 = $this->generateEXTID1();
        
        // Validate and save the model
        if (!$actionpoint->save()) {
            $this->errors = $actionpoint->getErrors();

            // Destroy the imph_import_header model, if we do that the database will remove the rest of the data
            $reference = $this->getFriendlyAttributes('IMPH', 'IMPH_REFERENCE') == null ? $this->attributes['IMPH_REFERENCE'] : $this->getFriendlyAttributes('IMPH', 'IMPH_REFERENCE');
            (new Imph_import_header)->destroy($reference);

            return false;
        }

        return true;
    }

    /**
     * Return the error messagebag
     *
     * @return Illuminate\Support\Fascades\MessageBag
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Save the record in the PTV database and mark it as importable
     * 
     * @return boolean
     */
    public function save()
    {
        $reference = $this->getFriendlyAttributes('IMPH', 'IMPH_REFERENCE') == null ? $this->attributes['IMPH_REFERENCE'] : $this->getFriendlyAttributes('IMPH', 'IMPH_REFERENCE');
        $importHeader = (new Imph_import_header)->find($reference);

        // Check if we can find the import header
        if (is_null($importHeader)) {
            return false;
        }

        // Update the data and return
        $importHeader->IMPH_PROCESS_CODE = '20';
        $importHeader->save();

        $this->errors = null;
        return true;
    }

    /**
     * Generate a correct EXTID1
     * this is the only field that is static and cannot be changed by the programmer
     *
     * @return string
     */
    protected function generateEXTID1()
    {
        $value = '';
        
        if (!is_null(config('ptv.friendly_naming.IORA_ORDER_ACTIONPOINT.IORA_POSTCODE'))) {
            $attributeName = config('ptv.friendly_naming.IORA_ORDER_ACTIONPOINT.IORA_POSTCODE');
        } else {
            $attributeName = 'IORA_POSTCODE';
        }
        
        if (isset($this->attributes[$attributeName])) {
            $value .= $this->attributes[$attributeName];
        }

        if (!is_null(config('ptv.friendly_naming.IORA_ORDER_ACTIONPOINT.IORA_HOUSENO'))) {
            $attributeName = config('ptv.friendly_naming.IORA_ORDER_ACTIONPOINT.IORA_HOUSENO');
        } else {
            $attributeName = 'IORA_HOUSENO';
        }
        
        if (isset($this->attributes[$attributeName])) {
            $value .= $this->attributes[$attributeName];
        }

        return $value;
    }
}