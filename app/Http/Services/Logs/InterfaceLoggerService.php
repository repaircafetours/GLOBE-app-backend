<?php

namespace App\Http\Services\Logs;

use App\Models\Column;
use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Model;

interface InterfaceLoggerService
{
    /**
     * Returns the list of Column models that changed between the persisted
     * state and the current (dirty) state of the model.
     *
     * @return Column[]
     */
    public function updatedColumns(Model $model): array;

    /**
     * Persists a log entry for an update/create action.
     */
    public function log(
        Model $model,
        Model $old,
        ?Volunteer $volunteer = null,
    ): void;

    /**
     * Persists a log entry for a delete action.
     */
    public function logDelete(Model $model, ?Volunteer $volunteer = null): void;
}
