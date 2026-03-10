<?php

namespace App\Http\Services;

use App\Models\Visitor;
use App\Http\Services\Logs\VisitorLoggerService;
use Illuminate\Database\Eloquent\Collection;

class VisitorService
{
    private VisitorLoggerService $logger;

    public function __construct(VisitorLoggerService $logger)
    {
        $this->logger = $logger;
    }

    public function save(Visitor $visitor): void
    {
        $isNew = !$visitor->id;
        $visitor->save();
        if (!$isNew) {
            $old = $this->getFromId($visitor->id);
            $this->logger->log($visitor, $old);
        } else {
            $this->logger->log($visitor, new Visitor());
        }
    }

    /**
     * Returns the old version of the current visitor. If it has not already been inserted in
     * database, returns a new empty visitor
     * @param Visitor $visitor
     * @return Visitor The databse instance of the requested Visitor, or an empty instance if it does not exists
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

    public function delete(Visitor $visitor): void
    {
        $this->logger->logDelete($visitor);
        $visitor->delete();
    }
}
