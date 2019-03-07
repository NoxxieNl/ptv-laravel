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
        'once'    => [
            'failed'  => [],
            'success' => [],
        ],
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

        // Loop every not checked model, only loop it when there is a actuall callback registerd
        if (count(self::$callback['failed']) > 0 or count(self::$callback['success']) > 0) {
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
            }
        }

        // Check if there are callback regisrerd that must be called once
        if (count(self::$callback['once']['failed']) > 0 or count(self::$callback['once']['success']) > 0) {

            // Execute failed callbacks if there are any registerd
            if (count(self::$callback['once']['failed']) > 0) {
                $failedImports = $notCheckedImports->filter(function ($model) {
                    return $model->IMPH_PROCESS_CODE == '-30';
                });

                if (count($failedImports) > 0) {
                    foreach (self::$callback['once']['failed'] as $callback) {
                        // The collection is send tho the callback function
                        call_user_func_array($callback, [$failedImports->values()]);
                    }
                }
            }

            // Execute success callbacks if there are any registerd
            if (count(self::$callback['once']['success']) > 0) {
                $successImports = $notCheckedImports->filter(function ($model) {
                    return $model->IMPH_PROCESS_CODE == '50';
                });

                if (count($successImports) > 0) {
                    foreach (self::$callback['once']['success'] as $callback) {
                        // The collection is send tho the callback function
                        call_user_func_array($callback, [$successImports->values()]);
                    }
                }
            }
        }
    }

    /**
     * Register a callback for this class.
     *
     * @param callable $callback
     * @param string   $type
     * @param bool     $once
     *
     * @return void
     */
    public static function registerCallback(callable $callback, string $type, bool $once = false)
    {
        if (!in_array($type, ['failed', 'success'])) {
            throw new InvalidArgumentException(sprintf('The specified type "%s" is a invalid type', $type));
        }

        if (!$once) {
            self::$callback[$type][] = $callback;
        } else {
            self::$callback['once'][$type][] = $callback;
        }
    }
}
