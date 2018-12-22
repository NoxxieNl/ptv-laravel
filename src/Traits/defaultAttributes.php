<?php
namespace Noxxie\Ptv\Traits;

use ReflectionClass;
use Carbon\Carbon;

use noxxie\Ptv\Models\Imph_import_header;

trait defaultAttributes {

    /**
     * Set default attributes for each model using this trait
     *
     * @return void
     */
    protected function setDefaultAttributes()
    {
        $configData = config('ptv.defaults.' . strtoupper((new ReflectionClass($this))->getShortName()));
        if (count($configData) > 0) {
            foreach(config('ptv.defaults.' . strtoupper((new ReflectionClass($this))->getShortName())) as $column => $value)
            {

                // Config value for current date in correct format
                if ($value == '%CURRENT_DATE%') {
                    $value = Carbon::now()->format('Ymd');
                }

                $this->attributes[$column] = $value;
            }
        }
    }
}