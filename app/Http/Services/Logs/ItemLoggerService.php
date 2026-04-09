<?php

namespace App\Http\Services\Logs;

use App\Http\Services\ItemService;
use App\Models\Logs\LogsItem;
use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Model;

class ItemLoggerService implements InterfaceLoggerService
{
    private ItemService $service;

    public function __construct(private LogsService $logsService) {}

    /**
     * À appeler une seule fois depuis le ServiceProvider
     * pour casser la dépendance circulaire.
     */
    public function initialize(ItemService $service): void
    {
        $this->service = $service;
    }

    /**
     * Persists a log entry for an update/create action on an item.
     *
     * @param Model          $new       The updated/created item
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

        $logsItem = new LogsItem();
        $logsItem->logs_id = $log->id;
        $logsItem->item_id = $new->id;
        // Enregistre le visiteur possesseur de l'objet au moment de la modification
        $logsItem->visitor_id = $new->visitor_id ?? null;
        $logsItem->save();
    }

    /**
     * Persists a log entry for a delete action on an item.
     *
     * @param Model          $model     The item being deleted
     * @param Volunteer|null $volunteer The volunteer who performed the action (null = system)
     */
    public function logDelete(Model $model, ?Volunteer $volunteer = null): void
    {
        $log = $this->logsService->create($volunteer);

        $logsItem = new LogsItem();
        $logsItem->logs_id = $log->id;
        $logsItem->item_id = $model->id;
        $logsItem->visitor_id = $model->visitor_id ?? null;
        $logsItem->save();
    }

    /**
     * Returns the list of columns that changed between the persisted
     * state and the current (dirty) state of the item.
     */
    public function updatedColumns(Model $model): array
    {
        $old = $this->service->getFromItem($model);
        return $this->logsService->buildUpdatedColumns($old, $model);
    }
}
