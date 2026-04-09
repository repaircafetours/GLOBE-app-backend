<?php

namespace App\Http\Services\Logs;

use App\Http\Services\VolunteerService;
use App\Models\Logs\LogsVolunteer;
use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Model;

class VolunteerLoggerService implements InterfaceLoggerService
{
    private VolunteerService $service;

    public function __construct(private LogsService $logsService) {}

    /**
     * À appeler une seule fois depuis le ServiceProvider
     * pour casser la dépendance circulaire.
     */
    public function initialize(VolunteerService $service): void
    {
        $this->service = $service;
    }

    /**
     * Persists a log entry for an update/create action on a volunteer.
     *
     * @param Model          $new       The updated/created volunteer
     * @param Model          $old       The previous state (empty model for creation)
     * @param Volunteer|null $volunteer The volunteer who performed the action (null = system)
     */
    public function log(
        Model $new,
        Model $old,
        ?Volunteer $volunteer = null,
    ): void {
        $columns = $this->logsService->buildUpdatedColumns($old, $new);

        $log = $this->logsService->create($volunteer);
        $this->logsService->attachColumns($log, $columns);

        $logsVolunteer = new LogsVolunteer();
        $logsVolunteer->logs_id = $log->id;
        $logsVolunteer->volunteer_id = $new->id;
        $logsVolunteer->save();
    }

    /**
     * Persists a log entry for a delete action on a volunteer.
     *
     * @param Model          $model     The volunteer being deleted
     * @param Volunteer|null $volunteer The volunteer who performed the action (null = system)
     */
    public function logDelete(Model $model, ?Volunteer $volunteer = null): void
    {
        $log = $this->logsService->create($volunteer);

        $logsVolunteer = new LogsVolunteer();
        $logsVolunteer->logs_id = $log->id;
        $logsVolunteer->volunteer_id = $model->id;
        $logsVolunteer->save();
    }

    public function updatedColumns(Model $model): array
    {
        $old = $this->service->getFromVolunteer($model);
        return $this->logsService->buildUpdatedColumns($old, $model);
    }
}
