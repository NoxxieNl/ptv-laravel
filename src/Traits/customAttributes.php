<?php
namespace Noxxie\Ptv\Traits;

use illuminate\support\collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

Trait customAttributes {

    /**
     * allowed prefixes in custom attributes
     *
     * @var array
     */
    private $allowedPrefixAttributes = [
        'IMPH', 'IORH', 'IORA'
    ];

    /**
     * Translation from prefix to table
     *
     * @var array
     */
    private $prefixToTable = [
        'IMPH' => 'IMPH_IMPORT_HEADER',
        'IORH' => 'IORH_ORDER_HEADER',
        'IORA' => 'IORA_ORDER_ACTIONPOINT'
    ];

    /**
     * Holds all the attributes
     * 
     * @var collection
     */
    private $cusomAttributes;

    /**
     * Check if the specified attributes exist
     *
     * @param collection $attributes
     * @return boolean
     */
    public function customAttributesExist(collection $attributes)
    {
        $this->validator = Validator::make($attributes->toArray(), []);
        foreach ($attributes as $attribute => $value)
        {
            $prefix = strtoupper(substr($attribute, 0, 4));
            if (!in_array($prefix, $this->allowedPrefixAttributes)) {
                $this->validator->getMessageBag()->add($attribute, 'The attribute ' . $attribute . ' has an unknown prefix');
            }

            if (!$this->hasColumn($this->prefixToTable[$prefix], strtoupper($attribute))) {
                $this->validator->getMessageBag()->add($attribute, 'The attribute ' . $attribute . ' does not exist in table ' . $this->prefixToTable[$prefix]);
            }
        }

        $this->cusomAttributes = $attributes;
    }

    /**
     * Get the custom attributes
     *
     * @param string $filter
     * @return collection
     */
    public function getCustomAttributes(string $filter = null)
    {
        if (is_null($filter)) {
            return $this->cusomAttributes;
        }
        
        return $this->cusomAttributes->filter(function ($value, $key) use ($filter) {
            return strtoupper(substr($key, 0, 4)) == $filter;
        });
    }

    /**
     * Filter the attributes that only the custom one remains
     *
     * @param collection $attributes
     * @return boolean
     */
    public function filterCustomAttributes(collection $attributes)
    {
        // Check if there are custom values and if so if they can exist
        list($customAttributes, $junk) = $attributes->partition(function ($value, $key) {
            return in_array(strtoupper(substr($key, 0, 4)), ['IMPH', 'IORA', 'IORH']);
        });

        if (!$this->customAttributesExist($customAttributes)) {
            return false;
        }
    }

    /**
     * Returns if there are any errors
     *
     * @return boolean
     */
    public function customAttributeHasError()
    {
        return count($this->validator->errors()) == 0 ? false : true;
    }

    /**
     * Returns the error messagebag
     *
     * @return collection
     */
    public function customAttributeGetErrors()
    {
        return $this->validator->errors();
    }

    /**
     * Check if the column exist in the table
     *
     * @param string $table
     * @param string $column
     * @return boolean
     */
    protected function hasColumn(string $table, string $column)
    {
        
        return Schema::connection(config('ptv.connection'))->hasColumn($table, $column);
    }
}