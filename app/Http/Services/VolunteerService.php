<?php

namespace App\Http\Services;

use App\Http\Services\Logs\VolunteerLoggerService;
use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Collection;

class VolunteerService
{
    private VolunteerLoggerService $logger;

    public function __construct(VolunteerLoggerService $logger)
    {
        $this->logger = $logger;
    }

    public function save(Volunteer $volunteer): void
    {
        if ($volunteer->exists) {
            $old = $volunteer->fresh();
            $volunteer->save();
            $this->logger->log($volunteer, $old ?? new Volunteer());
        } else {
            $volunteer->save();
            $this->logger->log($volunteer, new Volunteer());
        }
    }

    /**
     * Returns the current database instance of the volunteer,
     * or a new empty instance if it has not been saved yet.
     * @param Volunteer $volunteer
     * @return Volunteer The database instance of the requested volunteer, or an empty instance if it does not exist
     */
    public function getFromVolunteer(Volunteer $volunteer): Volunteer
    {
        return $volunteer->exists
            ? $volunteer->fresh() ?? new Volunteer()
            : new Volunteer();
    }

    public function getFromId(int $id): Volunteer
    {
        return Volunteer::find($id);
    }

    /**
     * @return Collection<int,Volunteer>
     */
    public function getAll(): Collection
    {
        return Volunteer::all();
    }

    public function delete(Volunteer $volunteer): void
    {
        $this->logger->logDelete($volunteer);
        $volunteer->delete();
    }
}
