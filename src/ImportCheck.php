<?php

namespace Noxxie\Ptv;

use InvalidArgumentException;
use Noxxie\Ptv\Models\Imph_import_header;

class ImportCheck
{
    /**
     * Contains all the registerd callbacks for this class.
     *
     * @var array
     */
    protected static $callback = [
        'failed'  => [],
        'success' => [],
    ];

    /**
     * Invoke command so this class can be called from a scheduled task within laravel.
     */
    public function __invoke()
    {
        $notCheckedImports = Imph_import_header::whereImportNotChecked()->whereIn('IMPH_PROCESS_CODE', ['50', '-30'])->get();

        // Nothing to check if the aren't any imported records
        if ($notCheckedImports->count() == 0) {
            return;
        }

        // Loop every not checked model
        foreach ($notCheckedImports as $importModel) {
            // Failed import
            if ($importModel->IMPH_PROCESS_CODE == '-30') {
                // Check if there are any callback registed and if so execute the code
                if (count(self::$callback['failed']) > 0) {
                    foreach (self::$callback['failed'] as $callback) {
                        // The model is send tho the callback function
                        call_user_func_array($callback, [$importModel]);
                    }
                }
            } else {
                // Check if there are any callback registed and if so execute the code
                if (count(self::$callback['success']) > 0) {
                    foreach (self::$callback['success'] as $callback) {
                        // The model is send tho the callback function
                        call_user_func_array($callback, [$importModel]);
                    }
                }
            }

            // And update the model that it is checked (use forceSave to overwrite the validation on the model)
            $importModel->IMPH_DESCRIPTION = 'IMPORT_CHECK_EXECUTED';
            $importModel->forceSave();
        }
    }

    /**
     * Register a callback for this class.
     *
     * @param callable $callback
     * @param string   $type
     *
     * @return void
     */
    public static function registerCallback(callable $callback, string $type)
    {
        if (!in_array($type, ['failed', 'success'])) {
            throw new InvalidArgumentException(sprintf('The specified type "%s" is a invalid type', $type));
        }

        self::$callback[$type][] = $callback;
    }
}
