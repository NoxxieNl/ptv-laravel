<?php

namespace Noxxie\Ptv\Traits;

Trait friendlyAttributes {

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
     * Translate the friendly attributes to the correct attributes
     *
     * @param string $filter
     * @param string $specific
     * @return void
     */
    public function getFriendlyAttributes(string $filter, string $specific = null)
    {
        $attributes = [];

        if (!array_key_exists($filter, $this->prefixToTable)) {
            throw new \exception($filter . ' is not a allowed table prefix');
        }

        $table = $this->prefixToTable[$filter];
        $configData = config('ptv.friendly_naming.' . strtoupper($table));

        foreach ($this->attributes as $key => $value) {
            
            if (in_array($key, $configData)) {
                $attributes[array_search($key, $configData)] = $value;
            }
        }

        if (!is_null($specific)) {
            if (isset($attributes[$specific])) {
                return $attributes[$specific];
            } else {
                return null;
            }
        }

        return $attributes;
    }
}