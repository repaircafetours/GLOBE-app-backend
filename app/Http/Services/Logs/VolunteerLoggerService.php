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

    public function log(
        Model $new,
        Model $old,
        ?Volunteer $volunteer = null,
    ): void {
        $columns = $this->logsService->buildUpdatedColumns($old, $new);

        $log = $this->logsService->create($volunteer);
        $this->logsService->attachColumns($log, $columns);

        $logsVisitor = new LogsVolunteer();
        $logsVisitor->logs_id = $log->id;
        $logsVisitor->volunteer_id = $new->id;
        $logsVisitor->save();
    }

    public function logDelete(Model $model, ?Volunteer $volunteer = null): void
    {
        $log = $this->logsService->create($volunteer);

        $logsVisitor = new LogsVolunteer();
        $logsVisitor->logs_id = $log->id;
        $logsVisitor->volunteer_id = $model->id;
        $logsVisitor->save();
    }

    public function updatedColumns(Model $model): array
    {
        $old = $this->service->getFromVolunteer($model);
        return $this->logsService->buildUpdatedColumns($old, $model);
    }
}
