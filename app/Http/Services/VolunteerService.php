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
        $isNew = !$volunteer->id;
        $volunteer->save();
        if (!$isNew) {
            $old = $this->getFromId($volunteer->id);
            $this->logger->log($volunteer, $old);
        } else {
            $this->logger->log($volunteer, new Volunteer());
        }
    }

    /**
     * Returns the old version of the current volunteer. If it has not already been inserted in
     * database, returns a new empty volunteer
     * @param volunteer $volunteer
     * @return volunteer The databse instance of the requested volunteer, or an empty instance if it does not exists
     */
    public function getFromVolunteer(Volunteer $volunteer): Volunteer
    {
        if (!$volunteer->id) {
            return new Volunteer();
        }
        return $this->getFromId($volunteer->id);
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
