<?php

namespace App\Http\Services\Logs;

use App\Http\Services\VisitorService;
use App\Models\Logs\LogsVisitor;
use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Model;

class VisitorLoggerService implements InterfaceLoggerService
{
    private VisitorService $service;

    public function __construct(private LogsService $logsService) {}

    /**
     * À appeler une seule fois depuis le ServiceProvider
     * pour casser la dépendance circulaire.
     */
    public function initialize(VisitorService $service): void
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

        $logsVisitor = new LogsVisitor();
        $logsVisitor->logs_id = $log->id;
        $logsVisitor->visitor_id = $new->id;
        $logsVisitor->save();
    }

    public function logDelete(Model $model, ?Volunteer $volunteer = null): void
    {
        $log = $this->logsService->create($volunteer);

        $logsVisitor = new LogsVisitor();
        $logsVisitor->logs_id = $log->id;
        $logsVisitor->visitor_id = $model->id;
        $logsVisitor->save();
    }

    public function updatedColumns(Model $model): array
    {
        $old = $this->service->getFromVisitor($model);
        return $this->logsService->buildUpdatedColumns($old, $model);
    }
}
