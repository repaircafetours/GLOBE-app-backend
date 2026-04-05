<?php

namespace App\Http\Services;

use App\Models\Visitor;
use App\Models\Volunteer;
use App\Http\Services\Logs\VisitorLoggerService;
use Illuminate\Database\Eloquent\Collection;

class VisitorService
{
    private VisitorLoggerService $logger;

    public function __construct(VisitorLoggerService $logger)
    {
        $this->logger = $logger;
    }

    public function save(Visitor $visitor, ?Volunteer $actor = null): void
    {
        $isNew = !$visitor->id;

        if ($isNew) {
            $visitor->save();
            $this->logger->log($visitor, new Visitor(), $actor);
        } else {
            // Fetch the old state BEFORE overwriting it with save()
            $old = $this->getFromId($visitor->id) ?? new Visitor();
            $visitor->save();
            $this->logger->log($visitor, $old, $actor);
        }
    }

    /**
     * Returns the old version of the current visitor. If it has not already been inserted in
     * database, returns a new empty visitor.
     *
     * @param Visitor $visitor
     * @return Visitor The database instance of the requested Visitor, or an empty instance if it does not exist
     */
    public function getFromVisitor(Visitor $visitor): Visitor
    {
        if (!$visitor->id) {
            return new Visitor();
        }
        return $this->getFromId($visitor->id);
    }

    public function getFromId(int $id): Visitor
    {
        return Visitor::find($id);
    }

    /**
     * @return Collection<int,Visitor>
     */
    public function getAll(): Collection
    {
        return Visitor::all();
    }

    public function delete(Visitor $visitor, ?Volunteer $actor = null): void
    {
        $this->logger->logDelete($visitor, $actor);
        $visitor->delete();
    }
}
