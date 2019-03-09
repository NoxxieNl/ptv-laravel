<?php
namespace Noxxie\Ptv\Helpers;

use Noxxie\Ptv\Models\Imph_import_header;
use RunTimeException;

class UniqueIdGeneration
{
    /**
     * Contains all the used ID's within the PTV transfer database
     *
     * @var Illuminate\Support\Collection
     */
    protected $usedReferences;

    /**
     * Constructor function because we use this class as a singleton it will be only called once
     */
    public function __construct()
    {
        // Load in the used references
        $this->usedReferences = Imph_import_header::all()->pluck('IMPH_REFERENCE');
    }

    /**
     * Generates a new unique ID
     *
     * @param int $maxRetries
     * @param int $retry
     *
     * @return void
     */
    public function generate(int $maxRetries = 10, int $retry = 0)
    {
        // Generate a new random id
        $id = mt_rand(1, 99999999);

        // Check if we are pass the treshhold of tries
        if ($retry >= $maxRetries) {
            throw new RunTimeException('Could not define a new unique ID');
        }

        // Check if the ID already exists within the used references stack if so rerun this method
        if ($this->usedReferences->contains($id)) {
            return $this->generate($maxRetries, ($retry + 1));
        }

        // Add the new ID to the usedReferences stack
        $this->usedReferences->push($id);

        // Return the id
        return $id;
    }

    /**
     * Removes an inserted ID from the used references stack
     *
     * @param int $id
     *
     * @return void
     */
    public function remove(int $id)
    {
        if ($this->usedReferences->contains($id)) {
            $this->usedReferences->forget($id);
        }
    }

    /**
     * Manually add an ID to the used references stack
     *
     * @param int $id
     *
     * @return void
     */
    public function add(int $id)
    {
        if (!$this->usedReferences->contains($id)) {
            $this->usedReferences->push($id);
        }
    }
}
