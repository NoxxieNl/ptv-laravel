<?php

namespace Noxxie\Ptv\Exceptions;

use Exception;
use Illuminate\Support\MessageBag;

class ModelValidationException extends Exception
{
    /**
     * Holds the defined errors.
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Create the exception instance with custom data.
     *
     * @param mixed      $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        if ($message instanceof MessageBag) {
            foreach ($message->getMessages() as $column => $reason) {
                $this->errors = [
                    'column' => $column,
                    'reason' => $reason,
                ];
            }
        }

        $message = 'Validation of model data failed';
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the setted errors.
     *
     * @return void
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
