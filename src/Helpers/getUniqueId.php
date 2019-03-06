<?php
namespace Noxxie\Ptv\Helpers;

use Noxxie\Ptv\Models\Imph_import_header;

class GetUniqueId
{
    /**
     * Generate a unique ID that can be inserted in to the PTV database
     *
     * @return integer
     */
    public static function generate()
    {
        $id = mt_rand(1, 99999999);

        // Make sure it doesnt exist in the database yet
        if (!is_null((new Imph_import_header)->find($id))) {
            self::generate();
        }

        return $id;
    }
}
