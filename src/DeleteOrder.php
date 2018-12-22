<?php
namespace Noxxie\Ptv;

use Noxxie\Ptv\Models\Imph_import_header;

use Noxxie\Ptv\Traits\friendlyAttributes;
use Noxxie\Ptv\Traits\customAttributes;

use Illuminate\Support\collection;

class DeleteOrder 
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
     * Create a new deletion order in PTV
     *
     * @param collection $collection
     * @return boolean
     */
    public function create(collection $collection)
    {
        // Overwrite IMPH_ACTION_CODE to delete
        $collection->put('IMPH_ACTION_CODE', 'DELETE');

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
}